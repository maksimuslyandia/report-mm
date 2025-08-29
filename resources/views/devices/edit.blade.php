@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Device</h1>

        <form action="{{ route('devices.update', $device) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="hostname" class="form-label">Hostname</label>
                <input type="text" name="hostname" class="form-control" value="{{ $device->hostname }}" required>
            </div>
            <button class="btn btn-success">Update</button>
            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
