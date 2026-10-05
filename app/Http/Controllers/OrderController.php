<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Sale;
use App\Mail\OrderNotificationMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view sales'])->only(['index', 'show', 'invoice']);
        $this->middleware(['auth', 'permission:create sales'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:create sales'])->only(['updateStatus', 'reserve', 'createSale']);
    }

    public function index()
    {
        $orders = Order::withCount('items')->withExists('sale')->latest('order_date')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $phones = Phone::with('brand')
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->orderBy('model')
            ->get()
            ->map(fn (Phone $phone) => [
                'id' => $phone->id,
                'label' => trim(($phone->brand?->name ?? 'Phone') . ' ' . ($phone->model ?? '')),
                'price' => (float) $phone->selling_price,
                'available' => max((int) $phone->quantity - (int) $phone->reserved_quantity, 0),
            ]);

        // Sales currently fulfil product inventory, so the order picker only offers
        // stock that can later be reserved and converted into a sale.
        $catalogItems = $phones->values();
        return view('orders.create', compact('catalogItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.phone_id' => ['nullable', 'integer', 'exists:phones,id'],
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                $subtotalCents = 0;
                $lines = [];

                foreach ($request->input('items') as $item) {
                    $quantity = (int) $item['quantity'];
                    $unitPriceCents = $this->moneyToCents($item['unit_price']);
                    $lineTotalCents = $quantity * $unitPriceCents;
                    $subtotalCents += $lineTotalCents;
                    $lines[] = compact('quantity', 'unitPriceCents', 'lineTotalCents') + ['description' => $item['description'], 'phone_id' => $item['phone_id'] ?? null];
                }

                $discountCents = $this->moneyToCents($request->input('discount_amount', 0));
                if ($discountCents > $subtotalCents) {
                    throw ValidationException::withMessages(['discount_amount' => ['Discount cannot exceed the order subtotal.']]);
                }

                $order = Order::create([
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'order_date' => $request->order_date,
                    'status' => $request->status,
                    'subtotal' => $this->centsToMoney($subtotalCents),
                    'discount_amount' => $this->centsToMoney($discountCents),
                    'total_amount' => $this->centsToMoney($subtotalCents - $discountCents),
                    'notes' => $request->notes,
                    'user_id' => auth()->id(),
                ]);

                $order->update(['invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
                foreach ($lines as $line) {
                    $order->items()->create([
                        'description' => $line['description'],
                        'phone_id' => $line['phone_id'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $this->centsToMoney($line['unitPriceCents']),
                        'line_total' => $this->centsToMoney($line['lineTotalCents']),
                    ]);
                }
                return $order;
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        $emailWarning = $this->sendCustomerEmail($order, 'placed');
        $redirect = redirect()->route('orders.show', $order)->with('success', 'Order created and invoice generated successfully.');
        return $emailWarning ? $redirect->with('warning', $emailWarning) : $redirect;
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');
        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order)
    {
        $order->load('items', 'user');
        return Pdf::loadView('pdf.order-invoice', compact('order'))->download($order->invoice_number . '.pdf');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => ['required', 'in:pending,confirmed,cancelled']]);

        if ($order->status === 'completed' && Sale::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'This order is completed because its sale has been recorded and cannot be reopened.');
        }

        $wasConfirmed = $order->status === 'confirmed';
        DB::transaction(function () use ($request, $order) {
            if ($request->status === 'cancelled' && $order->reserved_at) {
                $order->load('items');
                foreach ($order->items as $item) {
                    if ($item->phone_id && $item->reserved_quantity > 0) {
                        $phone = Phone::lockForUpdate()->find($item->phone_id);
                        if ($phone) {
                            $phone->update(['reserved_quantity' => max($phone->reserved_quantity - $item->reserved_quantity, 0)]);
                        }
                        $item->update(['reserved_quantity' => 0]);
                    }
                }
                $order->reserved_at = null;
            }
            $order->status = $request->status;
            $order->save();
        });
        $emailWarning = $request->status === 'confirmed' && ! $wasConfirmed
            ? $this->sendCustomerEmail($order, 'confirmed')
            : null;
        $redirect = back()->with('success', 'Order status updated.');
        return $emailWarning ? $redirect->with('warning', $emailWarning) : $redirect;
    }

    public function reserve(Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'Completed or cancelled orders cannot reserve stock.');
        }

        $wasConfirmed = $order->status === 'confirmed';
        try {
            DB::transaction(function () use ($order) {
                $order->load('items');
                if ($order->items->isEmpty() || $order->items->contains(fn ($item) => ! $item->phone_id)) {
                    throw ValidationException::withMessages(['order' => ['Only orders created from stocked products can be reserved.']]);
                }

                foreach ($order->items as $item) {
                    $phone = Phone::lockForUpdate()->findOrFail($item->phone_id);
                    $needed = $item->quantity - $item->reserved_quantity;
                    $available = $phone->quantity - $phone->reserved_quantity;
                    if ($needed > $available) {
                        throw ValidationException::withMessages(['order' => ["Only {$available} unit(s) remain available for {$item->description}."]]);
                    }
                    if ($needed > 0) {
                        $phone->increment('reserved_quantity', $needed);
                        $item->increment('reserved_quantity', $needed);
                    }
                }
                $order->update(['reserved_at' => now(), 'status' => 'confirmed']);
            });
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        $emailWarning = ! $wasConfirmed ? $this->sendCustomerEmail($order, 'confirmed') : null;
        $redirect = back()->with('success', 'Stock reserved for this order.');
        return $emailWarning ? $redirect->with('warning', $emailWarning) : $redirect;
    }

    public function createSale(Order $order)
    {
        $order->load('items.phone.brand');
        if (in_array($order->status, ['completed', 'cancelled'], true)) {
            return back()->with('error', 'This order cannot be converted into a sale.');
        }
        if ($order->items->isEmpty() || $order->items->contains(fn ($item) => ! $item->phone_id)) {
            return back()->with('error', 'Only orders created from stocked products can be converted into a sale.');
        }
        if ($order->reserved_at === null) {
            return back()->with('error', 'Reserve the order stock before creating its sale.');
        }

        return redirect()->route('sales.create', ['order' => $order->id]);
    }

    private function moneyToCents($amount): int { return (int) round((float) $amount * 100); }
    private function centsToMoney(int $cents): string { return number_format($cents / 100, 2, '.', ''); }

    private function sendCustomerEmail(Order $order, string $event): ?string
    {
        if (! $order->customer_email) {
            return null;
        }

        try {
            $order->loadMissing('items', 'user');
            $invoicePdf = Pdf::loadView('pdf.order-invoice', compact('order'))->output();
            Mail::to($order->customer_email)->send(new OrderNotificationMail($order, $event, $invoicePdf));
            return null;
        } catch (\Throwable $exception) {
            \Log::warning("Order {$event} email failed for order #{$order->id}: {$exception->getMessage()}");
            return 'The order was saved, but its customer email could not be sent. Please check mail settings.';
        }
    }
}
