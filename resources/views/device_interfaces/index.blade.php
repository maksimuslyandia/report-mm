@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Device Interfaces</h1>
        <a href="{{ route('device_interfaces.create') }}" class="btn btn-primary mb-3">Add Interface</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Device</th>
                <th>Parent Interface</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($interfaces as $interface)
                <tr>
                    <td>{{ $interface->id }}</td>
                    <td>{{ $interface->name }}</td>
                    <td>{{ $interface->device->hostname }}</td>
                    <td>{{ $interface->parentInterface?->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('device_interfaces.show', $interface) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('device_interfaces.edit', $interface) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('device_interfaces.destroy', $interface) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this interface?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
