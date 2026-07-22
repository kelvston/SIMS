<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Barryvdh\DomPDF\Facade\Pdf;

class BarcodeController extends Controller
{
    /**
     * Display the barcode generation form.
     */
    public function generateForm(): View
    {
        return view('barcodes.generate');
    }

    /**
     * Handle the form submission, generate the barcodes, and display them.
     */
    public function generate(Request $request): View
    {
        $validated = $request->validate([
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|min:1|gte:start_number|max:999999',
        ]);

        $barcodes = [];
        $generator = new BarcodeGeneratorSVG();
        $organizationName = Setting::where('key', 'organization_name')->value('value')
            ?? config('app.name', 'Pharmacy');

        for ($i = $validated['start_number']; $i <= $validated['end_number']; $i++) {
            $barcode_number = str_pad($i, 4, '0', STR_PAD_LEFT);
            $barcode_svg = $generator->getBarcode($barcode_number, $generator::TYPE_CODE_128);

            $barcodes[] = [
                'name' => $organizationName,
                'number' => $barcode_number,
                'svg' => $barcode_svg,
            ];
        }

        return view('barcodes.display', ['barcodes' => $barcodes]);
    }

    /**
     * Generate and download the PDF from the submitted data.
     */
    public function download(Request $request)
    {
        // Get the barcode data from the session or request
        // For this example, we'll assume the barcode data is passed through the request
        // You may want to use a session for more robust state management
        $barcodes = json_decode($request->input('barcodes'), true);

        // Load the simple PDF-specific view
        $pdf = Pdf::loadView('barcodes.pdf', ['barcodes' => $barcodes]);

        // Return the PDF for download
        return $pdf->download('barcodes.pdf');
    }
}
