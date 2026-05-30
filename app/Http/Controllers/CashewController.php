<?php

namespace App\Http\Controllers;

use App\Models\Cashew;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashewController extends Controller
{
    /**
     * Show receive form
     */
    public function showReceiveForm()
    {
        $products = Product::all()->pluck('name', 'id');
        return view('medicines.receive')->with(compact('products'));
    }

    /**
     * Store received cashews
     */
    public function storeReceivedcashews(Request $request)
    {
        $request->validate([
            'cashews' => 'required|array|min:1',
            'cashews.*.product_id' => 'required|exists:products,id',
            'cashews.*.unit' => 'required|string|max:50',
            'cashews.*.unit_price' => 'required|numeric|min:0',
            'cashews.*.selling_price' => 'required|numeric|min:0',
            'cashews.*.stock_origin' => 'nullable|string|max:255',
            'cashews.*.quantity' => 'required|integer|min:1',
            'cashews.*.low_stock_threshold' => 'nullable|integer|min:0',
            'cashews.*.condition' => 'nullable|string|max:255',
            'cashews.*.batch_number' => 'nullable|string|max:255',
            'cashews.*.received_at' => 'nullable|date',
            'cashews.*.description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->cashews as $item) {
                $receivedAt = $item['received_at'] ?? now()->toDateString();

                Cashew::create([
                    'product_id' => $item['product_id'],
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'selling_price' => $item['selling_price'],
                    'quantity' => $item['quantity'],
                    'low_stock_threshold' => $item['low_stock_threshold'] ?? 5,
                    'stock_origin' => $item['stock_origin'] ?? null,
                    'condition' => $item['condition'] ?? null,
                    'batch_number' => $item['batch_number'] ?: 'BATCH-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
                    'received_at' => $receivedAt,
                    'description' => $item['description'] ?? null,
                    'status' => 'available',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('cashews.index')
                ->with('success', 'Cashew stock received successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to receive cashew stock. ' . $e->getMessage());
        }
    }

    /**
     * Display all cashews
     */
    public function index()
    {
        $cashews = Cashew::with('product')->latest()->paginate(10);

        return view('cashews.index', compact('cashews'));
    }

    public function edit(Cashew $cashew)
    {
        $products = Product::orderBy('name')->get();

        return view('cashews.edit', compact('cashew', 'products'));
    }

    public function update(Request $request, Cashew $cashew)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'status' => 'required|string|max:50',
            'received_at' => 'nullable|date',
            'condition' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
        ]);

        $cashew->update($validated);

        return redirect()->route('cashews.index')->with('success', 'Cashew stock updated successfully.');
    }

    public function destroy(Cashew $cashew)
    {
        $cashew->delete();

        return redirect()->route('cashews.index')->with('success', 'Cashew stock deleted successfully.');
    }
}
