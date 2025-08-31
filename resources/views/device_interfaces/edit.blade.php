@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Device Interface</h1>

        <form action="{{ route('device_interfaces.update', $deviceInterface) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $deviceInterface->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Device</label>
                <select name="device_id" class="form-control" required>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ $device->id == $deviceInterface->device_id ? 'selected' : '' }}>
                            {{ $device->hostname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Parent Interface</label>
                <select name="device_interface_id" class="form-control">
                    <option value="">None</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ $parent->id == $deviceInterface->device_interface_id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('device_interfaces.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
