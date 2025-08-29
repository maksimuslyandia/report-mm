@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Device Details</h1>
        <p><strong>ID:</strong> {{ $device->id }}</p>
        <p><strong>Hostname:</strong> {{ $device->hostname }}</p>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Back</a>
    </div>
@endsection
