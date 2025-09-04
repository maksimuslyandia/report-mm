@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2>Edit WAN Stat</h2>
@include('layouts.errors')
        <form action="{{ route('wan_stats.update', $wan_stat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Link Name</label>
                <input type="text" name="link_name" value="{{ old('link_name', $wan_stat->link_name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Link Type</label>
                <input type="text" name="link_type" value="{{ old('link_type', $wan_stat->link_type) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Region</label>
                <input type="text" name="region" value="{{ old('region', $wan_stat->region) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Bandwidth (bits)</label>
                <input type="number" name="bandwidth_bits" value="{{ old('bandwidth_bits', $wan_stat->bandwidth_bits) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Traffic In</label>
                <input type="number" name="traffic_in" value="{{ old('traffic_in', $wan_stat->traffic_in) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Traffic Out</label>
                <input type="number" name="traffic_out" value="{{ old('traffic_out', $wan_stat->traffic_out) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">95% In</label>
                <input type="number" name="q_95_in" value="{{ old('q_95_in', $wan_stat->q_95_in) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">95% Out</label>
                <input type="number" name="q_95_out" value="{{ old('q_95_out', $wan_stat->q_95_out) }}" class="form-control" required>
            </div>
            <div hidden="" class="mb-3">
                <label class="form-label">Start Datetime</label>
                <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime', \Carbon\Carbon::parse($wan_stat->start_datetime)->format('Y-m-d\TH:i')) }}" class="form-control" required>
            </div>

            <div hidden=""  class="mb-3">
                <label class="form-label">End Datetime</label>
                <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime', \Carbon\Carbon::parse($wan_stat->end_datetime)->format('Y-m-d\TH:i')) }}" class="form-control" required>
            </div>


            <div class="mb-3">
                <label>Airport Code</label>
                <input type="text" name="airport_code"
                       value="{{ old('airport_code', $wan_stat->metaData->airport_code ?? '') }}"
                       class="form-control">
            </div>
            <div class="mb-3">
                <label>ISP Type</label>
                <input type="text" name="isp_type"
                       value="{{ old('isp_type', $wan_stat->metaData->isp_type ?? '') }}"
                       class="form-control">
            </div>
            <div class="mb-3">
                <input type="checkbox"
                       class="form-check-input"
                       id="is_ibo"
                       name="is_ibo"
                       value="1"
                       @if(isset($wan_stat->metaData) && $wan_stat->metaData->is_ibo) checked @endif>

                <label class="form-check-label" for="is_ibo">Is IBO</label>
            </div>

            <input type="hidden" name="wan_stat_total_id" value="{{ $wan_stat->id }}">

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('wan_stats.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
