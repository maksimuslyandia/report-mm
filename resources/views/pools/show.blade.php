@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Pool Details</h1>

        <div class="mb-3">
            <strong>ID:</strong> {{ $pool->id }}
        </div>

        <div class="mb-3">
            <strong>Name:</strong> {{ $pool->name }}
        </div>

        <div class="mb-3">
            <strong>Device:</strong> {{ $pool->device->hostname }}
        </div>

        <div class="mb-3">
            <strong>Interface:</strong> {{ $pool->interface->name }}
        </div>

        <a href="{{ route('pools.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('pools.edit', $pool) }}" class="btn btn-warning">Edit</a>
    </div>
@endsection
