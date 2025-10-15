@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Your System</h1>
        </div>

        <!-- Reports Tab Navigation -->
        <div class="flex flex-wrap gap-2 sm:space-x-4 mb-8">
            <!-- Sales Reports Tab -->
            @can('manage users')
            <a href="{{ route('users.index') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('users.index'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                <span class="mr-2">👥</span> Manage Users
            </a>
            @endcan

            @can('manage roles')
            <a href="{{ route('roles.index') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('roles.index'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                <span class="mr-2">🔐</span> Manage Roles
            </a>
            @endcan

            @can('manage products')
            <a href="{{ route('products.index') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('products.index'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                <span class="mr-2">🏷️</span> Manage Products
            </a>
            @endcan
            <!-- Expenses Report Tab -->
            <a href="{{ route('categories.index') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('categories.index'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                Manage Category
            </a>

            @can('Manage Setting')
            <a href="{{ route('settings.edit') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('settings.edit'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                <span class="mr-2">⚙️</span>Manage Settings
            </a>
            @endcan
        </div>

        <!-- Dynamic content area for the reports -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- This is a placeholder for the content of the currently selected report. -->
            <!-- You can include the actual report view here based on the selected tab. -->
            <p class="text-gray-600">
                Please select any option from the tabs above to view its content.
            </p>
        </div>
    </div>
@endsection
