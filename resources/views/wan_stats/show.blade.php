@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2>WAN Stat Detail</h2>

        <div class="card">
            <div class="card-body">
                <p><strong>Link Name:</strong> {{ $wan_stat->link_name }}</p>
                <p><strong>Type:</strong> {{ $wan_stat->link_type }}</p>
                <p><strong>Region:</strong> {{ $wan_stat->region }}</p>
                <p><strong>Bandwidth:</strong> {{ number_format($wan_stat->bandwidth_bits) }}</p>
                <p><strong>Traffic In:</strong> {{ number_format($wan_stat->traffic_in) }}</p>
                <p><strong>Traffic Out:</strong> {{ number_format($wan_stat->traffic_out) }}</p>
                <p><strong>95% In:</strong> {{ number_format($wan_stat->q_95_in) }}</p>
                <p><strong>95% Out:</strong> {{ number_format($wan_stat->q_95_out) }}</p>
                <p><strong>Start:</strong> {{ $wan_stat->start_datetime }}</p>
                <p><strong>End:</strong> {{ $wan_stat->end_datetime }}</p>
            </div>
        </div>

        <a href="{{ route('wan_stats.index') }}" class="btn btn-secondary mt-3">Back</a>
    </div>
@endsection
