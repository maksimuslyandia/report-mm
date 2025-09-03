@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2>Add WAN Stat</h2>

        <form action="{{ route('wan_stats.store') }}" method="POST">
            @csrf
            @include('wan_stats.form')

            <button type="submit" class="btn btn-primary mt-3">Save</button>
            <a href="{{ route('wan_stats.index') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
@endsection