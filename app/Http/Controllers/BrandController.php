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

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');

        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);

        if (!$header || !in_array('name', $header)) {
            fclose($handle);

            return back()->with('error', 'CSV must contain a name column.');
        }

        $nameIndex = array_search('name', $header);

        $products = [];

        while (($row = fgetcsv($handle)) !== false) {

            if (empty(array_filter($row))) {
                continue;
            }

            $name = trim($row[$nameIndex] ?? '');

            if ($name !== '') {
                $products[] = [
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        fclose($handle);

        if (empty($products)) {
            return back()->with('error', 'No products found in the CSV.');
        }

        Brand::insert($products);

        return back()->with(
            'success',
            count($products) . ' products uploaded successfully.'
        );
    }

    public function bulkTemplate()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // CSV header
            fputcsv($handle, ['name']);

            // Example products
            fputcsv($handle, ['Bold']);
            fputcsv($handle, ['Tyre']);
            fputcsv($handle, ['Coil']);

            fclose($handle);
        }, 'products-import-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
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


