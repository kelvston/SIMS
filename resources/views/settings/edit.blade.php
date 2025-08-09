@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-lg p-6 max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Organization Settings</h1>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="organization_name" class="block text-gray-700 text-sm font-bold mb-2">Organization Name</label>
                    <input type="text" id="organization_name" name="organization_name" value="{{ $settings['organization_name'] ?? '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <input type="text" id="organization_address" name="organization_address" value="{{ $settings['organization_address'] ?? '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_address')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_phone" class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                    <input type="text" id="organization_phone" name="organization_phone" value="{{ $settings['organization_phone'] ?? '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_phone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="organization_email" name="organization_email" value="{{ $settings['organization_email'] ?? '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="organization_logo" class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                    <input type="file" id="organization_logo" name="organization_logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('organization_logo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Current Logo:</p>
                            <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}" alt="Current Organization Logo" class="mt-2 h-20">
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
