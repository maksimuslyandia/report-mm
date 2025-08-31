@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Device Interface Details</h1>

        <div class="mb-3"><strong>ID:</strong> {{ $deviceInterface->id }}</div>
        <div class="mb-3"><strong>Name:</strong> {{ $deviceInterface->name }}</div>
        <div class="mb-3"><strong>Device:</strong> {{ $deviceInterface->device->hostname }}</div>
        <div class="mb-3"><strong>Parent Interface:</strong> {{ $deviceInterface->parentInterface?->name ?? '-' }}</div>

        <a href="{{ route('device_interfaces.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('device_interfaces.edit', $deviceInterface) }}" class="btn btn-warning">Edit</a>
    </div>
@endsection
