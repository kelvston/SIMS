@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative"> <!-- Added relative here -->
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">All Phones in Inventory</h1>

        @can('receive phones')
            <div class="flex justify-end mb-4">
                <a href="{{ route('phones.receive.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Receive New Phones
                </a>
            </div>
        @endcan

        @if ($phones->isEmpty())
            <p class="text-center text-gray-600">No phones found in inventory. Start by receiving new phones!</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 table-auto"> <!-- Added table-auto for better layout -->
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IMEI</th> <!-- reduced px to 4 -->
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Storage</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received At</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($phones as $phone)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->imei }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->brand->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->model }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->color }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->storage_capacity }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${{ number_format($phone->selling_price, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($phone->status == 'available') bg-green-100 text-green-800
                                @elseif($phone->status == 'sold') bg-red-100 text-red-800
                                @elseif($phone->status == 'under_installment') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $phone->status)) }}
                            </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->received_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $phones->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
