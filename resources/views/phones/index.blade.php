@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-5 sm:p-8 rounded-xl shadow-md relative">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Stock control</p><h1 class="mt-1 text-2xl font-semibold text-slate-800">Inventory</h1></div>
            @can('receive phones')<a href="{{ route('phones.receive.form') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">Receive inventory</a>@endcan
        </div>

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

        <h2 class="mb-4 text-base font-semibold text-slate-700">Products</h2>
        @if ($phones->isEmpty())
            <p class="text-center text-gray-600">No product found in inventory. Start by receiving new products!</p>
        @else
            <div class="table-scroll sm:rounded-lg sm:border sm:border-gray-200 sm:shadow-sm">
                <table class="responsive-table min-w-full divide-y divide-gray-200 table-auto"> <!-- Added table-auto for better layout -->
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Buying Price (Tsh)</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price (Tsh)</th>
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
                            <td data-label="IMEI" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->id }}</td>
                            <td data-label="Brand" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->brand->name ?? 'N/A' }}</td>
                            <td data-label="Model" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->model }}</td>
                            <td data-label="Storage" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $phone->quantity }}</td>
                            <td data-label="Buying Price (Tsh)" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tsh {{ number_format($phone->purchase_price, 2) }}</td>
                            <td data-label="Selling Price (Tsh)" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tsh {{ number_format($phone->selling_price, 2) }}</td>
                            <td data-label="Status" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $phone->status)) }}
                            </span>
                            </td>
                            <td data-label="Received At" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ optional($phone->received_at)->format('Y-m-d H:i') ?? 'N/A' }}</td>
                            @if(auth()->check() && auth()->user()->hasAnyPermission(['edit phones', 'delete phones']))
                                <td data-label="Actions" class="px-4 py-2 text-sm text-gray-900">
                                    @php
                                        $isReadOnly = in_array($phone->status, ['sold', 'under_installment'], true);
                                    @endphp

                                    @if($isReadOnly)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">View only</span>
                                    @else
                                        <div class="flex flex-wrap gap-2">
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
                                        </div>
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

    </div>
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure? This action cannot be undone!')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
@endsection
