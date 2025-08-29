@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Devices</h1>
        <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Add Device</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Hostname</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($devices as $device)
                <tr>
                    <td>{{ $device->id }}</td>
                    <td>{{ $device->hostname }}</td>
                    <td>
                        <a href="{{ route('devices.show', $device) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('devices.edit', $device) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('devices.destroy', $device) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this device?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
