@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2>Edit WAN Stat</h2>

        <form action="{{ route('wan_stats.update', $wanStatTotal->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('wan_stats.form')

            <button type="submit" class="btn btn-warning mt-3">Update</button>
            <a href="{{ route('wan_stats.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
@endsection
