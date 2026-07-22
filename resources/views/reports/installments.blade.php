@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Installments Report</h1>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form action="{{ route('reports.installments') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="status" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="active" @if(request('status') == 'active') selected @endif>Active</option>
                        <option value="paid" @if(request('status') == 'paid') selected @endif>Paid Off</option>
                        <option value="defaulted" @if(request('status') == 'defaulted') selected @endif>Defaulted</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if(request()->has('status'))
                        <a href="{{ route('reports.installments') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                            <i class="fas fa-undo-alt mr-2"></i>Reset
                        </a>
                    @endif
                    <a href="{{ route('reports.installments', array_merge(request()->query(), ['download' => 'true'])) }}" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Download CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <!-- Plans Summary Cards (Circle Design) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8 justify-center text-center">
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Total Plans</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalPlans) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Collected</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalCollectedAmount, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Pending</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalPendingAmount, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500">Completed</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalPlansCompleted) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">Defaulted</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalPlansDefaulted) }}</p>
            </div>
        </div>


        <!-- Installment Plans Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Installment Plans</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($installmentPlans as $plan)
                        @php
                            $totalPaid = $plan->installmentPayments->sum('amount_paid');
                            $remaining = ($plan->sale->final_amount ?? 0) - $totalPaid;

                            $statusClass = '';
                            if ($plan->status == 'paid') {
                                $statusClass = 'bg-green-100 text-green-800';
                            } elseif ($plan->status == 'defaulted') {
                                $statusClass = 'bg-red-100 text-red-800';
                            } else {
                                $statusClass = 'bg-yellow-100 text-yellow-800';
                            }
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan->sale->customer_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @foreach($plan->sale->saleItems as $item)
                                    @if($item->medicine)
                                        {{ $item->medicine->product->name ?? 'Medicine' }}<br>
                                    @elseif($item->cosmetic)
                                        {{ $item->cosmetic->name }}<br>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($plan->sale->final_amount ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($totalPaid, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-semibold">{{ number_format($remaining, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $plan->installmentPayments->last() ? $plan->installmentPayments->last()->payment_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No installment plans found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $installmentPlans->links() }}
            </div>
        </div>
    </div>
@endsection
