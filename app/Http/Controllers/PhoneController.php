<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Phone;
use App\Models\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
class PhoneController extends Controller
{
    // Add a constructor to apply middleware
    public function __construct()
    {
        $this->middleware(['auth', 'permission:receive phones'])->only('storeReceivedPhones');
        $this->middleware(['auth', 'permission:view phones'])->only(['index', 'showReceiveForm']);
    }

    /**
     * Show the form for receiving new phones.
     *
     * @return \Illuminate\View\View
     */
    public function showReceiveForm()
    {
        $brands = Brand::all();
        return view('phones.receive', compact('brands'));
    }

    /**
     * Store newly received phones in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReceivedPhones(Request $request)
    {
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
            $stockLevel = StockLevel::firstOrNew([
                'brand_id' => $request->brand_id,
                'model' => $request->model,
                'color' => $request->color,
            ]);
            $stockLevel->current_stock += $newPhonesCount;
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

    /**
     * Display a listing of the phones.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $phones = Phone::with('brand')->orderBy('received_at', 'desc')->paginate(10);
        return view('phones.index', compact('phones'));
    }
}
