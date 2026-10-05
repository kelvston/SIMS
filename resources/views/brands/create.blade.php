@extends('layouts.app')

@section('title', 'Create New Brand')
@section('subtitle', 'Add a new product brand to the system.')

@section('content')

    <div style="position: relative; background: white; padding: 32px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">

        {{-- Watermark --}}
        <img src="{{ asset('images/spare.png') }}"
             alt="Watermark"
             style="
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -30%);
            opacity: 0.15;
            width: 384px;
            pointer-events: none;
            z-index: 0;
         ">

        {{-- Content --}}
        <div style="position: relative; z-index: 10;">

            {{-- Page Title --}}
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                Create New Product
            </h1>


            {{-- Success --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Success!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            @endif


            {{-- Error --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Error!</strong>
                    <span>{{ session('error') }}</span>
                </div>
            @endif


            {{-- Validation --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Validation Error!</strong>

                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- ==========================================
                 CREATE SINGLE PRODUCT
            =========================================== --}}

            <form action="{{ route('brands.store') }}" method="POST">

                @csrf

                <div class="mb-4">

                    <label for="name"
                           class="block text-gray-700 text-sm font-bold mb-2">
                        Product Name:
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           placeholder="e.g., Bold, Tyre, Coil"
                           required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none">

                    @error('name')
                    <p class="text-red-500 text-xs italic">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                <div class="flex items-center justify-between">

                    <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full shadow-lg">
                        Create Product
                    </button>

                    <a href="{{ route('brands.index') }}"
                       class="font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </a>

                </div>

            </form>


            {{-- ==========================================
                 BULK UPLOAD
            =========================================== --}}

            <div class="border-t border-gray-200 mt-8 pt-8">

                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    Bulk Upload Products
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Upload multiple products using a CSV file.
                </p>


                {{-- ==========================================
                     DOWNLOAD TEMPLATE
                =========================================== --}}

                <div style="
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 24px;
            ">

                    <div>

                        <h3 style="
                        font-size: 18px;
                        font-weight: 700;
                        color: #1f2937;
                        margin: 0 0 5px 0;
                    ">
                            CSV Template
                        </h3>

                        <p style="
                        font-size: 14px;
                        color: #6b7280;
                        margin: 0;
                    ">
                            Download the template and add your products.
                        </p>

                    </div>


                    {{-- DOWNLOAD BUTTON --}}
                    <a href="{{ route('brands.bulk.template') }}"
                       style="
                        display: inline-block;
                        background-color: #374151;
                        color: #ffffff;
                        padding: 12px 20px;
                        border-radius: 8px;
                        font-weight: 700;
                        font-size: 14px;
                        text-decoration: none;
                        white-space: nowrap;
                        cursor: pointer;
                   ">
                        Download CSV Template
                    </a>

                </div>


                {{-- ==========================================
                     UPLOAD CSV
                =========================================== --}}

                <form action="{{ route('brands.bulk.upload') }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf

                    <div class="mb-4">

                        <label for="csv_file"
                               class="block text-gray-700 text-sm font-bold mb-2">
                            Select CSV File:
                        </label>

                        <input type="file"
                               name="csv_file"
                               id="csv_file"
                               accept=".csv,text/csv"
                               required
                               class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2">

                        @error('csv_file')
                        <p class="text-red-500 text-xs italic mt-1">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- CSV INFORMATION --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">

                        <p class="text-sm text-blue-700">
                            Your CSV should contain a
                            <strong>name</strong>
                            column.
                        </p>

                        <p class="text-xs text-blue-600 mt-1">
                            Example: Bold, Tyre, Coil
                        </p>

                    </div>


                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                        Upload Products
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection
