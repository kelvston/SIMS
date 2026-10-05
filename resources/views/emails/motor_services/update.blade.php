<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; background: #f4f6f8; color: #263238; font-family: Arial, sans-serif; }
        .card { max-width: 620px; margin: 28px auto; overflow: hidden; border-radius: 10px; background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        .header { padding: 26px 30px; background: #e76f24; color: #ffffff; }
        .body { padding: 26px 30px; line-height: 1.55; font-size: 14px; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .summary td { padding: 10px; border-bottom: 1px solid #e8edf0; }
        .summary td:first-child { width: 42%; color: #667085; }
        .footer { padding: 16px 30px; background: #f8fafb; color: #667085; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header"><h2 style="margin:0">{{ $action === 'created' ? 'Your service job has been received' : 'Your service job has been updated' }}</h2></div>
        <div class="body">
            <p>Hello {{ $service->vehicle->customer_name }},</p>
            <p>Here is the latest update for your vehicle service job.</p>
            <table class="summary">
                <tr><td>Job number</td><td><strong>{{ $service->job_number }}</strong></td></tr>
                <tr><td>Vehicle</td><td>{{ $service->vehicle->registration_number }} — {{ $service->vehicle->make }} {{ $service->vehicle->model }}</td></tr>
                <tr><td>Status</td><td>{{ ucwords(str_replace('_', ' ', $service->status)) }}</td></tr>
                <tr><td>Assigned mechanic</td><td>{{ $service->mechanic?->name ?? 'To be assigned' }}</td></tr>
                <tr><td>Diagnosis due by</td><td>{{ $service->diagnosis_due_date?->format('d M Y') ?? 'Not set' }}</td></tr>
                <tr><td>Amount due</td><td>Tsh {{ number_format($service->total_amount - $service->amount_paid, 2) }}</td></tr>
            </table>
            @if($service->diagnosis)
                <p><strong>Diagnosis:</strong><br>{{ $service->diagnosis }}</p>
            @endif
            @if($service->work_performed)
                <p><strong>Work performed:</strong><br>{{ $service->work_performed }}</p>
            @endif
        </div>
        <div class="footer">This is an automated service update from {{ config('app.name') }}.</div>
    </div>
</body>
</html>
