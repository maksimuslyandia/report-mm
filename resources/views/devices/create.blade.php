@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add Device</h1>

        <form action="{{ route('devices.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="hostname" class="form-label">Hostname</label>
                <input type="text" name="hostname" class="form-control" required>
            </div>
            <button class="btn btn-success">Save</button>
            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
