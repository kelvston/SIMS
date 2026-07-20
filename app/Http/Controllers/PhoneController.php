<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\AccessoryStock;
use App\Models\Color;
use App\Models\Phone;
use App\Models\PhoneModel;
use App\Models\PhoneStorageCapacity;
use App\Models\Product;
use App\Models\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling

class PhoneController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    // Add a constructor to apply middleware
    public function __construct()
    {
        // Only authenticated users with 'receive phones' permission can access storeReceivedPhones
        $this->middleware(['auth', 'permission:receive phones'])->only('storeReceivedPhones');
        // Only authenticated users with 'view phones' permission can access index and showReceiveForm
        $this->middleware(['auth', 'permission:view phones'])->only(['index', 'showReceiveForm']);
    }

    /**
     * Show the form for receiving new phones.
     *
     * @return \Illuminate\View\View
     */
    public function showReceiveForm()
    {
        // This method is now protected by 'permission:view phones' middleware
        $brands = Brand::with([
            'phoneModels' => fn ($query) => $query->orderBy('name'),
            'phoneModels.colors' => fn ($query) => $query->orderBy('name'),
            'phoneModels.storageCapacities' => fn ($query) => $query->orderBy('name'),
        ])->orderBy('name')->get();

        $brandOptions = $brands->mapWithKeys(function ($brand) {
            return [
                $brand->id => [
                    'name' => $brand->name,
                    'models' => $brand->phoneModels->mapWithKeys(function ($model) {
                        return [
                            $model->name => [
                                'colors' => $model->colors->pluck('name')->values(),
                                'storage_capacities' => $model->storageCapacities->pluck('name')->values(),
                            ],
                        ];
                    }),
                ],
            ];
        });

        $accessories = Product::withSum(['accessoryStocks as available_quantity' => function ($query) {
            $query->where('status', 'available');
        }], 'quantity')->orderBy('name')->get();

        return view('phones.receive', compact('brands', 'brandOptions', 'accessories'));
    }

    /**
     * Store newly received phones in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReceivedPhones(Request $request)
    {
        $productType = $request->input('product_type', 'phone');

        if ($productType === 'accessory') {
            return $this->storeReceivedAccessories($request);
        }

        // This method is now protected by 'permission:receive phones' middleware
        // ... (rest of your existing storeReceivedPhones logic) ...
        $request->merge([
            'imeis' => collect($request->input('imeis', []))
                ->map(fn ($imei) => preg_replace('/\s+/', '', trim((string) $imei)))
                ->filter()
                ->values()
                ->all(),
            'model' => trim((string) $request->input('model')),
            'color' => trim((string) $request->input('color')),
            'storage_capacity' => trim((string) $request->input('storage_capacity')),
        ]);

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'storage_capacity' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'imeis' => 'required|array|min:1',
            'imeis.*' => 'required|string|distinct|unique:phones,imei|max:255',
        ]);

        try {
            DB::beginTransaction();

            $brand = Brand::findOrFail($request->brand_id);
            $newPhonesCount = 0;
            $phoneModel = PhoneModel::firstOrCreate([
                'brand_id' => $brand->id,
                'name' => $request->model,
            ]);

            Color::firstOrCreate([
                'phone_model_id' => $phoneModel->id,
                'name' => $request->color,
            ]);

            PhoneStorageCapacity::firstOrCreate([
                'phone_model_id' => $phoneModel->id,
                'name' => $request->storage_capacity,
            ]);

            foreach ($request->imeis as $imei) {
                Phone::create([
                    'imei' => $imei,
                    'model' => $request->model,
                    'brand_id' => $request->brand_id,
                    'color' => $request->color,
                    'storage_capacity' => $request->storage_capacity,
                    'purchase_price' => $request->purchase_price,
                    'selling_price' => $request->selling_price,
                    'status' => 'available',
                    'received_at' => now(),
                ]);
                $newPhonesCount++;
            }

            // Update StockLevel: Find or create the stock entry and increment the count
            $stockLevel = StockLevel::firstOrNew([
                'brand_id' => $request->brand_id,
                'model' => $request->model,
                'color' => $request->color,
            ]);
            $stockLevel->current_stock = (int) $stockLevel->current_stock + $newPhonesCount;
            $stockLevel->last_updated_at = now();
            $stockLevel->save();

            DB::commit();

            return redirect()->back()->with('success', $newPhonesCount . ' phone(s) received successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error receiving phones: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to receive phones. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }

    private function storeReceivedAccessories(Request $request)
    {
        $request->merge([
            'accessory_name' => trim((string) $request->input('accessory_name')),
            'accessory_quantity' => $request->filled('accessory_quantity') ? $request->input('accessory_quantity') : 0,
            'accessory_unit' => trim((string) $request->input('accessory_unit', 'piece')) ?: 'piece',
            'accessory_low_stock_threshold' => $request->filled('accessory_low_stock_threshold') ? $request->input('accessory_low_stock_threshold') : 5,
        ]);

        $request->validate([
            'product_type' => 'required|in:phone,accessory',
            'accessory_name' => 'required|string|max:255',
            'accessory_quantity' => 'required|integer|min:1',
            'accessory_purchase_price' => 'required|numeric|min:0',
            'accessory_selling_price' => 'required|numeric|min:0|gte:accessory_purchase_price',
            'accessory_unit' => 'required|string|max:50',
            'accessory_low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::firstOrCreate(['name' => $request->accessory_name]);

            AccessoryStock::create([
                'product_id' => $product->id,
                'status' => 'available',
                'received_at' => now()->toDateString(),
                'batch_number' => 'ACC-' . now()->format('YmdHis'),
                'condition' => 'new',
                'quantity' => (int) $request->accessory_quantity,
                'unit' => $request->accessory_unit,
                'unit_price' => $request->accessory_purchase_price,
                'selling_price' => $request->accessory_selling_price,
                'barcode' => null,
                'user_id' => auth()->id(),
                'low_stock_threshold' => (int) $request->accessory_low_stock_threshold,
            ]);

            DB::commit();

            return redirect()->back()->with('success', $request->accessory_quantity . ' ' . $request->accessory_unit . '(s) of ' . $product->name . ' received successfully!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error receiving accessories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to receive accessories. Please try again. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display a listing of the phones.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // This method is now protected by 'permission:view phones' middleware
        $phones = Phone::with(['brand', 'saleItem'])->orderBy('received_at', 'desc')->paginate(10);
        $accessories = Product::withSum(['accessoryStocks as current_stock' => function ($query) {
            $query->where('status', 'available');
        }], 'quantity')
            ->withMax(['accessoryStocks as selling_price' => function ($query) {
                $query->where('status', 'available')->where('quantity', '>', 0);
            }], 'selling_price')
            ->withMax(['accessoryStocks as unit_price' => function ($query) {
                $query->where('status', 'available')->where('quantity', '>', 0);
            }], 'unit_price')
            ->withMax(['accessoryStocks as low_stock_threshold' => function ($query) {
                $query->where('status', 'available');
            }], 'low_stock_threshold')
            ->orderBy('name')
            ->get();

        return view('phones.index', compact('phones', 'accessories'));
    }
}
