<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Medicine;
use App\Models\StockLevel;
use App\Models\Cosmetic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException; // Import for better error handling

class MedicineController extends Controller // <<< IMPORTANT: Ensure it extends App\Http\Controllers\Controller
{
    // Add a constructor to apply middleware
    public function __construct()
    {
        // Only authenticated users with 'receive medicines' permission can access storeReceivedMedicines
        $this->middleware(['auth', 'permission:receive medicines'])->only('storeReceivedMedicines');
        // Only authenticated users with 'view medicines' permission can access index and showReceiveForm
        $this->middleware(['auth', 'permission:view medicines'])->only(['index', 'showReceiveForm']);
    }

    /**
     * Show the form for receiving new medicines.
     *
     * @return \Illuminate\View\View
     */
    public function showReceiveForm()
    {
        // This method is now protected by 'permission:view medicines' middleware
        $products = Product::all();
        $cosmetic_categories = DB::table('cosmetic_categories')->get();
        $cosmetics = DB::table('cosmetics')->get();
        return view('medicines.receive', compact('products', 'cosmetic_categories','cosmetics'));
    }

    /**
     * Store newly received medicines in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReceivedMedicines(Request $request)
    {
//dd($request->all());
        $request->validate([
            'medicines' => 'required_without:cosmetics|array|min:1',
            'cosmetics' => 'required_without:medicines|array|min:1',

            'medicines.*.product_id' => 'required|exists:products,id',
            'medicines.*.description' => 'required|string|max:255',
            'medicines.*.stock_origin' => 'required|string|max:255',
            'medicines.*.storage_capacity' => 'required|string|max:255',
            'medicines.*.purchase_price' => 'required|numeric|min:0',
            'medicines.*.selling_price' => 'required|numeric|min:0|gte:medicines.*.purchase_price',
            'medicines.*.imeis' => 'required|array|min:1',

            // --- NEW VALIDATION RULES FOR NESTED IMEI AND CONDITION ---
            'medicines.*.imeis.*.imei' => 'required|string|distinct|unique:medicines,imei|max:255',
            'medicines.*.imeis.*.condition' => 'required|string|in:New,Used',
            // --- END NEW VALIDATION RULES ---

            // Validation rules for each medicine item
            'cosmetics.*.name' => 'required|string|max:255',
            'cosmetics.*.product_id' => 'nullable|exists:products,id',
            'cosmetics.*.category_id' => 'nullable|exists:cosmetic_categories,id',
            'cosmetics.*.barcode' => 'nullable|string|unique:cosmetics,barcode|max:255',
            'cosmetics.*.unit' => 'required|string|max:255',
            'cosmetics.*.description' => 'required|string|max:255',
            'cosmetics.*.stock_origin' => 'required|string|max:255',
            'cosmetics.*.purchase_price' => 'required|numeric|min:0',
            'cosmetics.*.selling_price' => 'required|numeric|min:0|gte:cosmetics.*.purchase_price',
            'cosmetics.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $newMedicinesCount = 0;
            $updatedAccessoriesCount = 0;

            // --- Handle Medicine Receiving ---
            if ($request->has('medicines')) {
                foreach ($request->medicines as $medicineData) {
                    $newMedicinesInGroup = 0;

                    // Create Medicine records for each IMEI
                    foreach ($medicineData['imeis'] as $imeiData) {
                        Medicine::create([
                            'product_id' => $medicineData['product_id'],
                            'storage_capacity' => $medicineData['storage_capacity'],
                            'purchase_price' => $medicineData['purchase_price'],
                            'selling_price' => $medicineData['selling_price'],
                            'description' => $medicineData['description'],
                            'stock_origin' => $medicineData['stock_origin'],
                            'condition' => $imeiData['condition'],
                            'status' => 'available',
                            'received_at' => now(),
                        ]);

                        $newMedicinesInGroup++;
                        $newMedicinesCount++;
                    }

                    // Update StockLevel for this specific medicine model
                    $stockLevel = StockLevel::firstOrNew([
                        'product_id' => $medicineData['product_id'],
                    ]);
                    $stockLevel->current_stock += $newMedicinesInGroup;
                    $stockLevel->last_updated_at = now();
                    $stockLevel->save();
                }

            }

            // --- Handle Accessory Receiving (UPDATED) ---
            if ($request->has('cosmetics')) {
                foreach ($request->cosmetics as $medicineData) {
                    $category = DB::table('cosmetic_categories')->find($medicineData['category_id'])->name;

                    $medicine = new Cosmetic();
                    $medicine->fill([
                        'name' => $medicineData['name'],
                        'category' => $category,
                        'barcode' => $medicineData['barcode'],
                        'unit' => $medicineData['unit'],
                        'purchase_price' => $medicineData['purchase_price'],
                        'selling_price' => $medicineData['selling_price'],
                        'description' => $medicineData['description'],
                        'stock_origin' => $medicineData['stock_origin'],
                        'quantity' => $medicineData['quantity'],
                        'status' => 'in_stock',
                    ]);

                    $medicine->save();
                }
            }

            DB::commit();

            $message = '';
            if ($newMedicinesCount > 0 && $updatedAccessoriesCount > 0) {
                $message = "Successfully received {$newMedicinesCount} medicine(s) and {$updatedAccessoriesCount} medicine/ies!";
            } elseif ($newMedicinesCount > 0) {
                $message = "Successfully received {$newMedicinesCount} medicine(s)!";
            } elseif ($updatedAccessoriesCount > 0) {
                $message = "Successfully received {$updatedAccessoriesCount} medicine/ies!";
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
     * Display a listing of the medicines.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // This method is now protected by 'permission:view medicines' middleware
        $medicines = Medicine::with('product')
            ->leftJoin('sale_items','sale_items.medicine_id','medicines.id')->whereNull('sale_items.medicine_id')
            ->orderBy('received_at', 'desc')->paginate(7);
        $cosmetics = Cosmetic::with('product')->orderBy('created_at', 'desc')->paginate(10);
        return view('medicines.index', compact('medicines','cosmetics'));
    }
}
