@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2>WAN Stat Detail</h2>

        <div class="card">
            <div class="card-body">
                <p><strong>Link Name:</strong> {{ $wanStatTotal->link_name }}</p>
                <p><strong>Type:</strong> {{ $wanStatTotal->link_type }}</p>
                <p><strong>Region:</strong> {{ $wanStatTotal->region }}</p>
                <p><strong>Bandwidth:</strong> {{ number_format($wanStatTotal->bandwidth_bits) }}</p>
                <p><strong>Traffic In:</strong> {{ number_format($wanStatTotal->traffic_in) }}</p>
                <p><strong>Traffic Out:</strong> {{ number_format($wanStatTotal->traffic_out) }}</p>
                <p><strong>95% In:</strong> {{ number_format($wanStatTotal->q_95_in) }}</p>
                <p><strong>95% Out:</strong> {{ number_format($wanStatTotal->q_95_out) }}</p>
                <p><strong>Start:</strong> {{ $wanStatTotal->start_datetime }}</p>
                <p><strong>End:</strong> {{ $wanStatTotal->end_datetime }}</p>
            </div>
        </div>

        <a href="{{ route('wan_stats.index') }}" class="btn btn-secondary mt-3">Back</a>
    </div>
@endsection
