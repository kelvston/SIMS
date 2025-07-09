<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\UnauthorizedException;

class BrandController extends Controller
{
    public function __construct()
    {
        // Only authenticated users with 'manage brands' permission can access these actions
        $this->middleware(['auth', 'permission:manage brands']);
    }

    /**
     * Display a listing of the brands.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $brands = Brand::paginate(10);
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
        ]);

        Brand::create([
            'name' => $request->name,
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully!');
    }

    /**
     * Show the form for editing the specified brand.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\View\View
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('brands')->ignore($brand->id)],
        ]);

        $brand->name = $request->name;
        $brand->save();

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully!');
    }

    /**
     * Remove the specified brand from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        // Before deleting a brand, consider if you want to prevent deletion if phones are associated
        // Or, implement a soft delete, or reassign associated phones to a 'N/A' brand.
        // For simplicity, cascade delete is set up in migration, so phones will be deleted.
        // You might want to add a confirmation message or check for associated phones first.

        if ($brand->phones()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete brand with associated phones. Please reassign or delete phones first.');
        }

        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully!');
    }
}


