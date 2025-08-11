<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Phone;
use App\Models\StockLevel;
use App\Models\Accessory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $brands = Brand::all();
        $colors = DB::table('colors')->get();
        $models = DB::table('model')->get();
        $accessory_categories = DB::table('accessory_categories')->get();
        $accessories = DB::table('accessories')->get();
        return view('phones.receive', compact('brands', 'colors', 'models', 'accessory_categories','accessories'));
    }

    /**
     * Store newly received phones in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReceivedPhones(Request $request)
    {
//dd($request->all());
        $request->validate([
            'phones' => 'required_without:accessories|array|min:1',
            'accessories' => 'required_without:phones|array|min:1',

            'phones.*.brand_id' => 'required|exists:brands,id',
            'phones.*.model' => 'required|string|max:255',
            'phones.*.color' => 'required|string|max:255',
            'phones.*.description' => 'required|string|max:255',
            'phones.*.stock_origin' => 'required|string|max:255',
            'phones.*.storage_capacity' => 'required|string|max:255',
            'phones.*.purchase_price' => 'required|numeric|min:0',
            'phones.*.selling_price' => 'required|numeric|min:0|gte:phones.*.purchase_price',
            'phones.*.imeis' => 'required|array|min:1',

            // --- NEW VALIDATION RULES FOR NESTED IMEI AND CONDITION ---
            'phones.*.imeis.*.imei' => 'required|string|distinct|unique:phones,imei|max:255',
            'phones.*.imeis.*.condition' => 'required|string|in:New,Used',
            // --- END NEW VALIDATION RULES ---

            // Validation rules for each accessory item
            'accessories.*.name' => 'required|string|max:255',
            'accessories.*.brand_id' => 'nullable|exists:brands,id',
            'accessories.*.category_id' => 'nullable|exists:accessory_categories,id',
            'accessories.*.barcode' => 'nullable|string|unique:accessories,barcode|max:255',
            'accessories.*.unit' => 'required|string|max:255',
            'accessories.*.description' => 'required|string|max:255',
            'accessories.*.stock_origin' => 'required|string|max:255',
            'accessories.*.purchase_price' => 'required|numeric|min:0',
            'accessories.*.selling_price' => 'required|numeric|min:0|gte:accessories.*.purchase_price',
            'accessories.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $newPhonesCount = 0;
            $updatedAccessoriesCount = 0;

            // --- Handle Phone Receiving ---
            if ($request->has('phones')) {
                foreach ($request->phones as $phoneData) {
                    $newPhonesInGroup = 0;

                    // Create Phone records for each IMEI
                    foreach ($phoneData['imeis'] as $imeiData) {
                        Phone::create([
                            'imei' => $imeiData['imei'],
                            'model' => $phoneData['model'],
                            'brand_id' => $phoneData['brand_id'],
                            'color' => $phoneData['color'],
                            'storage_capacity' => $phoneData['storage_capacity'],
                            'purchase_price' => $phoneData['purchase_price'],
                            'selling_price' => $phoneData['selling_price'],
                            'description' => $phoneData['description'],
                            'stock_origin' => $phoneData['stock_origin'],
                            'condition' => $imeiData['condition'],
                            'status' => 'available',
                            'received_at' => now(),
                        ]);

                        $newPhonesInGroup++;
                        $newPhonesCount++;
                    }

                    // Update StockLevel for this specific phone model
                    $stockLevel = StockLevel::firstOrNew([
                        'brand_id' => $phoneData['brand_id'],
                        'model' => $phoneData['model'],
                        'color' => $phoneData['color'],
                    ]);
                    $stockLevel->current_stock += $newPhonesInGroup;
                    $stockLevel->last_updated_at = now();
                    $stockLevel->save();
                }

            }

            // --- Handle Accessory Receiving (UPDATED) ---
            if ($request->has('accessories')) {
                foreach ($request->accessories as $accessoryData) {
                    $category = DB::table('accessory_categories')->find($accessoryData['category_id'])->name;

                    $accessory = new Accessory();
                    $accessory->fill([
                        'name' => $accessoryData['name'],
                        'category' => $category,
                        'barcode' => $accessoryData['barcode'],
                        'unit' => $accessoryData['unit'],
                        'purchase_price' => $accessoryData['purchase_price'],
                        'selling_price' => $accessoryData['selling_price'],
                        'description' => $accessoryData['description'],
                        'stock_origin' => $accessoryData['stock_origin'],
                        'quantity' => $accessoryData['quantity'],
                        'status' => 'in_stock',
                    ]);

                    $accessory->save();
                }
            }

            DB::commit();

            $message = '';
            if ($newPhonesCount > 0 && $updatedAccessoriesCount > 0) {
                $message = "Successfully received {$newPhonesCount} phone(s) and {$updatedAccessoriesCount} accessory/ies!";
            } elseif ($newPhonesCount > 0) {
                $message = "Successfully received {$newPhonesCount} phone(s)!";
            } elseif ($updatedAccessoriesCount > 0) {
                $message = "Successfully received {$updatedAccessoriesCount} accessory/ies!";
            } else {
                $message = 'new inventory was received.';
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error receiving inventory: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to receive inventory. Please try again.')->withInput();
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
        $phones = Phone::with('brand')
            ->leftJoin('sale_items','sale_items.phone_id','phones.id')->whereNull('sale_items.phone_id')
            ->orderBy('received_at', 'desc')->paginate(7);
        $accessories = Accessory::with('brand')->orderBy('created_at', 'desc')->paginate(10);
        return view('phones.index', compact('phones','accessories'));
    }
}
