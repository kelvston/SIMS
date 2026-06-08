@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Reports</h1>
        </div>

        <!-- Reports Tab Navigation -->
        <div class="flex flex-wrap gap-2 sm:space-x-4 mb-8">
            <!-- Sales Reports Tab -->
            <a href="{{ route('reports.sales') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.sales'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Sales Reports
            </a>

            <!-- Stock Reports Tab -->
            <a href="{{ route('reports.stock') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.stock'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Stock Reports
            </a>

            <!-- Profit & Loss Reports Tab -->
            <a href="{{ route('reports.profit_loss') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.profit_loss'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Profit & Loss
            </a>

            <!-- Expenses Report Tab -->
            <a href="{{ route('reports.expenses') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.expenses'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Expenses Report
            </a>

            <!-- Installments Report Tab -->
            <a href="{{ route('reports.installments') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.installments'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Installments Report
            </a>

            <!-- User Activity Report Tab -->
            <a href="{{ route('reports.users') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.users'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                User Activity Report
            </a>
            <a href="{{ route('reports.general') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.general'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                General Report
            </a>
            <a href="{{ route('reports.credit_sale') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.credit_sale'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Credit Sale Report
            </a>
            <a href="{{ route('reports.customers') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.customers'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Customers Report
            </a>
            <a href="{{ route('reports.stock_adjustments') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('reports.stock_adjustments'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Stock Adjustment Report
            </a>
        </div>

        <!-- Dynamic content area for the reports -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- This is a placeholder for the content of the currently selected report. -->
            <!-- You can include the actual report view here based on the selected tab. -->
            <p class="text-gray-600">
                Please select a report from the tabs above to view its content.
            </p>
        </div>
    </div>
@endsection
