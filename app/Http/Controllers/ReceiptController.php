<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:generate receipts']);
    }

    /**
     * Display a printable receipt for a given sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function show(Sale $sale)
    {
        $sale->load('saleItems.phone.brand');
        return view('receipts.show', compact('sale'));
    }
}
