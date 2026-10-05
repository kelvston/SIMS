@extends('layouts.app')
@section('title', 'New Motor Service')
@section('content')<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow"><div class="mb-6 flex flex-wrap justify-between gap-3"><h1 class="text-2xl font-semibold">New motor service job card</h1><a class="rounded-lg border px-4 py-2 text-sm font-semibold" href="{{ route('vehicles.create') }}">Register vehicle</a></div><form method="POST" action="{{ route('motor-services.store') }}">@include('motor_services.form')</form></div>@endsection
