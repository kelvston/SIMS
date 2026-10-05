@extends('layouts.app')
@section('title', 'Edit Job Card')
@section('content')<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow"><h1 class="mb-6 text-2xl font-semibold">Edit {{ $motorService->job_number }}</h1><form method="POST" action="{{ route('motor-services.update', $motorService) }}">@include('motor_services.form')</form></div>@endsection
