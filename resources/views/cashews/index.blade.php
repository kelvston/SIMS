@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            All  Products in Inventory
        </h1>

        @can('receive cashews')
            <div class="flex justify-end mb-4">
                <a href="{{ route('cashew.receive.form') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Receive New Cashew Products
                </a>
            </div>
        @endcan

        @if ($cashews->isEmpty())
            <p class="text-center text-gray-600">
                No cashew products found in inventory. Start by receiving new stock!
            </p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product Code
                        </th>


                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product Quantity
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Low Stock Threshold
                        </th>


                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Buying Price (TZS)
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Selling Price (TZS)
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Received At
                        </th>

                        @canany(['edit cashews', 'delete cashews'])
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        @endcanany
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($cashews as $cashew)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->product_id }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->product->name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->quantity }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->low_stock_threshold }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->unit }}
                            </td>


                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($cashew->unit_price, 2) }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($cashew->selling_price, 2) }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($cashew->status == 'available') bg-green-100 text-green-800
                                    @elseif($cashew->status == 'sold') bg-red-100 text-red-800
                                    @elseif($cashew->status == 'reserved') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">

                                    {{ ucfirst(str_replace('_', ' ', $cashew->status)) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $cashew->created_at->format('Y-m-d H:i') }}
                            </td>

                            <td class="px-4 py-2 flex gap-2">
                                @can('edit cashews')
                                    <a href="{{ route('cashews.edit', $cashew->id) }}"
                                       class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete cashews')
                                    <button type="button"
                                            onclick="confirmDelete({{ $cashew->id }})"
                                            class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                        Delete
                                    </button>

                                    <form id="delete-form-{{ $cashew->id }}"
                                          action="{{ route('cashews.destroy', $cashew->id) }}"
                                          method="POST"
                                          style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $cashews->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

    <script>
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
        @endif
    </script>
@endsection
