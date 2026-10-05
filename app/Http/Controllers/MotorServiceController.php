<?php

namespace App\Http\Controllers;

use App\Mail\MotorServiceUpdateMail;
use App\Models\MotorService;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MotorServiceController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        if ($this->isMechanic($request)) {
            return redirect()->route('motor-services.my-pending');
        }
        $this->requirePermission($request, 'view motor services');

        $services = MotorService::with(['vehicle', 'mechanic'])
            ->when($request->search, fn ($q, $search) => $q->where('job_number', 'like', "%{$search}%")->orWhereHas('vehicle', fn ($v) => $v->where('registration_number', 'like', "%{$search}%")->orWhere('customer_name', 'like', "%{$search}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('service_date')->paginate(15)->withQueryString();
        return view('motor_services.index', compact('services'));
    }

    public function myPending(Request $request)
    {
        if (! $this->isMechanic($request)) {
            $this->requirePermission($request, 'view motor services');
        }
        $services = MotorService::with(['vehicle', 'mechanic'])
            ->where('mechanic_id', $request->user()->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->when($request->search, fn ($q, $search) => $q->where('job_number', 'like', "%{$search}%")->orWhereHas('vehicle', fn ($v) => $v->where('registration_number', 'like', "%{$search}%")->orWhere('customer_name', 'like', "%{$search}%")))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderByRaw('diagnosis_due_date is null, diagnosis_due_date')
            ->latest('service_date')
            ->paginate(15)
            ->withQueryString();

        $isMyQueue = true;

        return view('motor_services.index', compact('services', 'isMyQueue'));
    }

    public function create(Request $request) { $this->denyMechanic($request); $this->requirePermission($request, 'create motor service jobs'); return view('motor_services.create', ['vehicles' => Vehicle::orderBy('registration_number')->get(), 'mechanics' => $this->mechanics()]); }

    public function createVehicle(Request $request) { $this->denyMechanic($request); $this->requirePermission($request, 'create motor service jobs'); return view('motor_services.vehicle_create'); }

    public function storeVehicle(Request $request)
    {
        $this->denyMechanic($request);
        $this->requirePermission($request, 'create motor service jobs');
        $vehicle = Vehicle::create($request->validate(['customer_name'=>['required','string','max:255'], 'customer_phone'=>['nullable','string','max:255'], 'customer_email'=>['nullable','email','max:255'], 'registration_number'=>['required','string','max:100','unique:vehicles,registration_number'], 'make'=>['required','string','max:100'], 'model'=>['nullable','string','max:100'], 'year'=>['nullable','integer','min:1900','max:'.(now()->year + 1)], 'color'=>['nullable','string','max:100'], 'vin'=>['nullable','string','max:100']]));
        return redirect()->route('motor-services.create', ['vehicle_id' => $vehicle->id])->with('success', 'Vehicle registered. Create its service job card.');
    }

    public function store(Request $request)
    {
        $this->denyMechanic($request);
        $this->requirePermission($request, 'create motor service jobs');
        $data = $this->validated($request);
        $data['total_amount'] = max(0, $data['labor_amount'] + $data['parts_amount'] - $data['discount_amount']);
        $data['payment_status'] = $this->paymentStatus($data['amount_paid'], $data['total_amount']);
        $service = MotorService::create($data + ['job_number' => 'JOB-'.now()->format('Ymd').'-'.str_pad((string)(MotorService::max('id') + 1), 5, '0', STR_PAD_LEFT)]);
        $this->sendCustomerUpdate($service, 'created');
        return redirect()->route('motor-services.show', $service)->with('success', 'Motor service job card created.');
    }

    public function show(Request $request, MotorService $motorService) { $this->authorizeServiceAccess($request, $motorService); $motorService->load('vehicle', 'mechanic'); return view('motor_services.show', compact('motorService')); }
    public function edit(Request $request, MotorService $motorService) { $this->authorizeServiceAccess($request, $motorService); $isMechanic = $this->isMechanic($request); return view('motor_services.edit', ['motorService'=>$motorService, 'vehicles'=>$isMechanic ? collect() : Vehicle::orderBy('registration_number')->get(), 'mechanics'=>$isMechanic ? collect() : $this->mechanics(), 'isMechanic'=>$isMechanic]); }

    public function update(Request $request, MotorService $motorService)
    {
        $this->authorizeServiceAccess($request, $motorService);

        if ($this->isMechanic($request)) {
            $data = $this->mechanicValidated($request);
            $data['completed_at'] = $data['status'] === 'completed' ? ($motorService->completed_at ?? now()) : null;
            $motorService->update($data);
            $this->sendCustomerUpdate($motorService, 'updated');

            return redirect()->route('motor-services.show', $motorService)->with('success', 'Job status and diagnosis updated.');
        }

        $data = $this->validated($request);
        $data['total_amount'] = max(0, $data['labor_amount'] + $data['parts_amount'] - $data['discount_amount']);
        $data['payment_status'] = $this->paymentStatus($data['amount_paid'], $data['total_amount']);
        $data['completed_at'] = $data['status'] === 'completed' ? ($motorService->completed_at ?? now()) : null;
        $motorService->update($data);
        $this->sendCustomerUpdate($motorService, 'updated');
        return redirect()->route('motor-services.show', $motorService)->with('success', 'Job card updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['vehicle_id'=>['required','exists:vehicles,id'], 'mechanic_id'=>['nullable','exists:users,id'], 'service_date'=>['required','date'], 'diagnosis_due_date'=>['nullable','date','after_or_equal:service_date'], 'complaint'=>['required','string','max:3000'], 'diagnosis'=>['nullable','string','max:3000'], 'work_performed'=>['nullable','string','max:3000'], 'labor_amount'=>['required','numeric','min:0'], 'parts_amount'=>['required','numeric','min:0'], 'discount_amount'=>['required','numeric','min:0'], 'amount_paid'=>['required','numeric','min:0'], 'status'=>['required','in:received,diagnosing,in_progress,waiting_parts,completed,cancelled'], 'notes'=>['nullable','string','max:3000']]);
    }
    private function mechanicValidated(Request $request): array
    {
        return $request->validate(['diagnosis'=>['nullable','string','max:3000'], 'work_performed'=>['nullable','string','max:3000'], 'status'=>['required','in:received,diagnosing,in_progress,waiting_parts,completed,cancelled'], 'notes'=>['nullable','string','max:3000']]);
    }
    private function paymentStatus(float $paid, float $total): string { return $paid >= $total && $total > 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'); }

    private function isMechanic(Request $request): bool { return $request->user()->hasAnyRole(['mechanic', 'mechanics']); }
    private function mechanics() { return User::role(['mechanic', 'mechanics'])->orderBy('name')->get(); }
    private function denyMechanic(Request $request): void { abort_if($this->isMechanic($request), 403); }
    private function requirePermission(Request $request, string $permission): void { abort_unless($request->user()->can($permission), 403); }
    private function authorizeServiceAccess(Request $request, MotorService $service): void { if ($this->isMechanic($request)) { abort_unless($service->mechanic_id === $request->user()->id && $request->user()->can('update assigned service jobs'), 403); return; } $this->requirePermission($request, 'view motor services'); }

    private function sendCustomerUpdate(MotorService $service, string $action): void
    {
        $service->loadMissing(['vehicle', 'mechanic']);

        if (! $service->vehicle?->customer_email) {
            return;
        }

        try {
            Mail::to($service->vehicle->customer_email)->send(new MotorServiceUpdateMail($service, $action));
        } catch (\Throwable $exception) {
            Log::warning('Service customer email could not be sent.', [
                'service_id' => $service->id,
                'customer_email' => $service->vehicle->customer_email,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
