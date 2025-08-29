@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Pool</h1>

        <form action="{{ route('pools.update', $pool) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $pool->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Device</label>
                <select name="device_id" class="form-control" required>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ $device->id == $pool->device_id ? 'selected' : '' }}>
                            {{ $device->hostname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Interface</label>
                <select name="interface_id" class="form-control" required>
                    @foreach($interfaces as $interface)
                        <option value="{{ $interface->id }}" {{ $interface->id == $pool->interface_id ? 'selected' : '' }}>
                            {{ $interface->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('pools.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
