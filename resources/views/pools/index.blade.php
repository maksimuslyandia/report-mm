@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Pools</h1>
        <a href="{{ route('pools.create') }}" class="btn btn-primary mb-3">Add Pool</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Device</th>
                <th>Interface</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pools as $pool)
                <tr>
                    <td>{{ $pool->id }}</td>
                    <td>{{ $pool->name }}</td>
                    <td>{{ $pool->device->hostname }}</td>
                    <td>{{ $pool->interface->name }}</td>
                    <td>
                        <a href="{{ route('pools.show', $pool) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('pools.edit', $pool) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('pools.destroy', $pool) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this pool?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
