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

            @can('manage brands')
            <a href="{{ route('brands.index') }}" class="py-2 px-6 text-center text-sm font-semibold rounded-lg shadow-md transition duration-300
                    @if(request()->routeIs('brands.index'))
                        bg-white text-indigo-600 border-b-2 border-indigo-600
                    @else
                        bg-gray-200 text-gray-700 hover:bg-gray-300
                    @endif">
                <span class="mr-2">🏷️</span> Manage Brands
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



{{--    @canany(['manage users', 'manage roles', 'manage brands'])--}}
{{--        <button @click="manageOpen = !manageOpen"--}}
{{--                class="w-full text-left py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center transition"--}}
{{--                :aria-expanded="manageOpen.toString()" aria-controls="manage-menu">--}}
{{--            <span><span class="mr-2">⚙️</span> Manage</span>--}}
{{--            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"--}}
{{--                 :class="{'rotate-180': manageOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"--}}
{{--                 stroke-width="2">--}}
{{--                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>--}}
{{--            </svg>--}}
{{--        </button>--}}
{{--        <div id="manage-menu" x-show="manageOpen" x-collapse class="pl-4 space-y-1 mt-1 overflow-hidden">--}}
{{--            @can('manage users')--}}
{{--                <a href="{{ route('users.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">--}}
{{--                    <span class="mr-2">👥</span> Manage Users--}}
{{--                </a>--}}
{{--            @endcan--}}
{{--            @can('manage roles')--}}
{{--                <a href="{{ route('roles.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">--}}
{{--                    <span class="mr-2">🔐</span> Manage Roles--}}
{{--                </a>--}}
{{--            @endcan--}}
{{--            @can('manage brands')--}}
{{--                <a href="{{ route('brands.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">--}}
{{--                    <span class="mr-2">🏷️</span> Manage Brands--}}
{{--                </a>--}}
{{--            @endcan--}}
{{--            @can('Manage Setting')--}}
{{--                <a href="{{ route('settings.edit') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">--}}
{{--                    <span class="mr-2">🏷️</span> Manage Settings--}}
{{--                </a>--}}
{{--            @endcan--}}
{{--        </div>--}}
{{--    @endcanany--}}



@endsection
