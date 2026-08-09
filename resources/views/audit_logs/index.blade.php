@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Audit Logs</h1>
                <p class="text-sm text-gray-600">Track who did what, when, and from where.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
            <select name="action" class="border-gray-300 rounded-md shadow-sm">
                <option value="">All actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>

            <select name="user_id" class="border-gray-300 rounded-md shadow-sm">
                <option value="">All users</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}" class="border-gray-300 rounded-md shadow-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="border-gray-300 rounded-md shadow-sm">

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-[#AD5D29] text-white rounded-md hover:bg-[#8f4c22]">
                    Filter
                </button>
                <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Record</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Changes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($auditLogs as $log)
                    <tr class="align-top">
                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $log->user?->name ?? 'System' }}
                            @if ($log->user?->email)
                                <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800 font-semibold">{{ $log->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ class_basename($log->auditable_type) ?: 'N/A' }}
                            @if ($log->auditable_id)
                                <div class="text-xs text-gray-500">ID: {{ $log->auditable_id }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700 min-w-[18rem]">
                            @if ($log->old_values)
                                <details class="mb-2">
                                    <summary class="cursor-pointer font-semibold">Old values</summary>
                                    <pre class="mt-2 whitespace-pre-wrap bg-gray-50 p-2 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </details>
                            @endif
                            @if ($log->new_values)
                                <details>
                                    <summary class="cursor-pointer font-semibold">New values</summary>
                                    <pre class="mt-2 whitespace-pre-wrap bg-gray-50 p-2 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </details>
                            @endif
                            @if (! $log->old_values && ! $log->new_values)
                                <span class="text-gray-400">No field changes</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <div>{{ $log->ip_address ?? 'N/A' }}</div>
                            <div>{{ $log->method }} {{ $log->url }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No audit logs found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $auditLogs->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
