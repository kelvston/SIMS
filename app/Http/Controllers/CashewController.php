<?php

namespace App\Http\Controllers;

use App\Models\Cashew;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'cashews.*.product_id' => 'required',
            'cashews.*.unit_price' => 'required|numeric|min:0',
            'cashews.*.selling_price' => 'required|numeric|min:0',
            'cashews.*.stock_origin' => 'nullable|string|max:255',
            'cashews.*.quantity' => 'required|integer|min:1',
            'cashews.*.low_stock_threshold' => 'nullable|integer|min:0',
            // size/color rows are optional per product
            'cashews.*.sizes'               => 'nullable|array',
            'cashews.*.sizes.*.size'        => 'required_with:cashews.*.sizes|string',
            'cashews.*.sizes.*.color'       => 'required_with:cashews.*.sizes|string',
            'cashews.*.sizes.*.quantity'    => 'required_with:cashews.*.sizes|integer|min:0',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->cashews as $item) {
                Cashew::create([
                    'product_id' => $item['product_id'],
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'selling_price' => $item['selling_price'],
                    'quantity' => $item['quantity'],
                    'low_stock_threshold' => $item['low_stock_threshold'] ?? 5,
                    'status' => 'available',
                    'received_at' => now(),
                    'user_id' => auth()->id(),
                ]);

                // Store size/color variants if provided
                if (!empty($item['sizes']) && is_array($item['sizes'])) {
                    foreach ($item['sizes'] as $sizeRow) {
                        if (!empty($sizeRow['size']) && !empty($sizeRow['color'])) {
                            ProductSize::create([
                                'product_id' => $item['product_id'],
                                'size'       => strtoupper($sizeRow['size']),
                                'color'      => strtoupper($sizeRow['color']),
                                'quantity'   => $sizeRow['quantity'] ?? 0,
                            ]);
                        }
                    }
                }
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
        $cashews = Cashew::with(['product', 'productSizes', 'receivedBy'])->latest()->paginate(10);

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

    public function searchForSale(Request $request)
    {
        $query = $request->get('q', '');

        $results = Cashew::with(['product', 'productSizes'])
            ->whereHas('product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('productSizes', function ($q) use ($query) {
                $q->where('size', 'like', "%{$query}%")
                    ->orWhere('color', 'like', "%{$query}%");
            })
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($cashew) {
                $variants = $cashew->productSizes->map(fn($s) => "{$s->size}/{$s->color} (qty:{$s->quantity})");
                return [
                    'id'            => $cashew->id,
                    'product_id'    => $cashew->product_id,
                    'name'          => $cashew->product->name,
                    'display_name'  => $cashew->product->name
                        . ($variants->count()
                            ? ' — ' . $variants->implode(', ')
                            : ''),
                    'selling_price' => $cashew->selling_price,
                    'unit'          => $cashew->unit,
                    'quantity'      => $cashew->quantity,
                    'sizes'         => $cashew->productSizes,
                ];
            });

        return response()->json($results);
    }
}
