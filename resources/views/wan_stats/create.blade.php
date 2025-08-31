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
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Add WAN Stat</h1>

        <form action="{{ route('wan_stats.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('wan_stats.form')
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Save
            </button>
        </form>
    </div>
@endsection
