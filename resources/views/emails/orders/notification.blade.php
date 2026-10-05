<!doctype html>
<html lang="en">
<body style="font-family:Arial,sans-serif;color:#1f2937;line-height:1.5">
    <h2>Hello {{ $order->customer_name }},</h2>
    @if($event === 'confirmed')
        <p>Your order <strong>{{ $order->invoice_number }}</strong> has been confirmed.</p>
        <p>We have reserved the requested stock and will process your sale shortly.</p>
    @else
        <p>We have received your order <strong>{{ $order->invoice_number }}</strong>.</p>
        <p>Our team will confirm availability and contact you if needed.</p>
    @endif
    <p><strong>Order total:</strong> Tsh {{ number_format($order->total_amount, 2) }}</p>
    <p>Your order invoice is attached for your records.</p>
    <p>Thank you,<br>{{ config('app.name') }}</p>
</body>
</html>
