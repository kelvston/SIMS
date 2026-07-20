@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative"> <!-- Added relative here -->
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Inventory</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @can('receive phones')
            <div class="flex justify-end mb-4">
                <a href="{{ route('phones.receive.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Receive Inventory
                </a>
            </div>
        @endcan

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Phones</h2>
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
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Buying Price (TZS)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price (TZS)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received At</th>
                        @if(auth()->check() && auth()->user()->hasAnyPermission(['edit phones', 'delete phones']))
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($phones as $phone)
                        @php
                            $statusClasses = match ($phone->status) {
                                'available' => 'bg-green-100 text-green-800',
                                'sold' => 'bg-red-100 text-red-800',
                                'under_installment' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->imei }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->brand->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->model }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->color }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->storage_capacity }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($phone->purchase_price, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($phone->selling_price, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $phone->status)) }}
                            </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ optional($phone->received_at)->format('Y-m-d H:i') ?? 'N/A' }}</td>
                            @if(auth()->check() && auth()->user()->hasAnyPermission(['edit phones', 'delete phones']))
                                <td class="px-4 py-2 flex gap-2">
                                    @php
                                        $isReadOnly = in_array($phone->status, ['sold', 'under_installment'], true) || $phone->saleItem;
                                    @endphp

                                    @if($isReadOnly)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">View only</span>
                                    @else
                                        @can('edit phones')
                                            <a href="{{ route('phones.edit', $phone->id) }}"
                                               class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Edit
                                            </a>
                                        @endcan

                                        @can('delete phones')
                                            <button type="button" onclick="confirmDelete({{ $phone->id }})" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                Delete
                                            </button>

                                            <form id="delete-form-{{ $phone->id }}" action="{{ route('phones.destroy', $phone->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $phones->links('pagination::tailwind') }}
            </div>
        @endif

        <h2 class="text-2xl font-semibold text-gray-700 mt-10 mb-4">Accessories</h2>
        @if ($accessories->isEmpty())
            <p class="text-center text-gray-600">No accessories found in inventory. Receive accessories from the inventory receiving page.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accessory</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buying Price (TZS)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price (TZS)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Threshold</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @can('edit phones')
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($accessories as $accessory)
                    
                        @php
                            $currentStock = (int) ($accessory->current_stock ?? 0);
                            $threshold = (int) ($accessory->low_stock_threshold ?? 5);
                        @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $accessory->name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $currentStock }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($accessory->unit_price ?? 0, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($accessory->selling_price ?? 0, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $threshold }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $currentStock <= $threshold ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $currentStock <= $threshold ? 'Low Stock' : 'Available' }}
                                </span>
                            </td>
                            @can('edit phones')
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('accessories.edit', $accessory->id) }}"
                                       class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Edit
                                    </a>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure? This action cannot be undone!')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
