<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ProductController extends Controller
{
    public function __construct()
    {
        // Only authenticated users with 'manage products' permission can access these actions
        $this->middleware(['auth', 'permission:manage products']);
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
        ]);

        Product::create([
            'name' => $request->name,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
        ]);

        $product->name = $request->name;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Before deleting a product, consider if you want to prevent deletion if medicines are associated
        // Or, implement a soft delete, or reassign associated medicines to a 'N/A' product.
        // For simplicity, cascade delete is set up in migration, so medicines will be deleted.
        // You might want to add a confirmation message or check for associated medicines first.

        if ($product->medicines()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete product with associated medicines. Please reassign or delete medicines first.');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'products'       => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
        ]);
        $now = now();
        $rows = collect($request->products)->map(fn($p) => [
            'name'       => trim($p['name']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        // Insert in chunks to avoid huge queries
        foreach ($rows->chunk(100) as $chunk) {
            DB::table('products')->insert($chunk->toArray());
        }
        return response()->json([
            'message' => $rows->count() . ' products added successfully.'
        ]);
    }
}


