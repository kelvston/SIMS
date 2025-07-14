@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">All Installment Plans</h1>

        @if ($installmentPlans->isEmpty())
            <p class="text-center text-gray-600">No installment plans found.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phones</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Installments</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($installmentPlans as $plan)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $plan->sale->customer_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($plan->sale->saleItems as $item)
                                        <li>{{ $item->phone->brand->name }} {{ $item->phone->model }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${{ number_format($plan->sale->final_amount, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${{ number_format($plan->installment_amount, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $plan->total_installments }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $plan->next_payment_date ? $plan->next_payment_date->format('Y-m-d') : 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($plan->status == 'active') bg-green-100 text-green-800
                                @elseif($plan->status == 'completed') bg-blue-100 text-blue-800
                                @elseif($plan->status == 'defaulted') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($plan->status) }}
                            </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                @can('record installment payments')
                                    @if ($plan->status == 'active')
                                        <a href="{{ route('installments.pay.form', $plan->id) }}" class="text-purple-600 hover:text-purple-900">Record Payment</a>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $installmentPlans->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
