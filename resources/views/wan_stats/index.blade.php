@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>WAN Stat Totals</h2>
            <a href="{{ route('wan_stats.create') }}" class="btn btn-primary">+ Add New</a>
            <a href="{{ route('wan_stats.export') }}" class="btn btn-success mb-3">
                Download Last Month CSV
            </a>
            <a href="{{ route('wan_stats.get-inactive-pots.csv') }}" class="btn btn-warning mb-3">
                Download Inactive ports -90d
            </a>

            <a href="{{ route('wan_stats.totals') }}" class="btn btn-warning mb-3">
                Download Last Month Totals CSV
            </a>

        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">{{ $message }}</div>
        @endif

        @php
            // Group by start_date (only date part)
            $grouped = $wanStats->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->start_datetime)->format('Y-m-d');
            });
        @endphp

                <!-- Tabs -->
        <ul class="nav nav-tabs" id="wanTabs" role="tablist">
            @foreach ($grouped as $date => $stats)
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if ($loop->first) active @endif"
                            id="tab-{{ $loop->index }}"
                            data-bs-toggle="tab"
                            data-bs-target="#content-{{ $loop->index }}"
                            type="button" role="tab">
                        {{ $date }}
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3">
            @foreach ($grouped as $date => $stats)
                <div class="tab-pane fade @if ($loop->first) show active @endif"
                     id="content-{{ $loop->index }}"
                     role="tabpanel">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>Link Name</th>
                                <th>Type</th>
                                <th>Region</th>
                                <th>Bandwidth</th>
                                <th>Traffic In</th>
                                <th>Traffic Out</th>
                                <th>95% In</th>
                                <th>95% Out</th>
                                <th>Airport Code</th>
                                <th>ISP Type</th>
                                <th>Is IBO</th> <!-- NEW -->
                                <th>Start</th>
                                <th>End</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($stats as $stat)
                                <tr>
                                    <td>{{ $stat->link_name }}</td>
                                    <td>{{ $stat->link_type }}</td>
                                    <td>{{ $stat->region }}</td>
                                    <td>{{ number_format($stat->bandwidth_bits) }}</td>
                                    <td>{{ number_format($stat->traffic_in) }}</td>
                                    <td>{{ number_format($stat->traffic_out) }}</td>
                                    <td>{{ number_format($stat->q_95_in) }}</td>
                                    <td>{{ number_format($stat->q_95_out) }}</td>
                                    <td>{{ $stat->metaData->airport_code ?? '-' }}</td>
                                    <td>{{ $stat->metaData->isp_type ?? '-' }}</td>
                                    <td>{{ $stat->metaData?->is_ibo ? 'Yes' : 'No' }}</td>
                                    <td>{{ $stat->start_datetime }}</td>
                                    <td>{{ $stat->end_datetime }}</td>
                                    <td>
                                        <a href="{{ route('wan_stats.show', $stat->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('wan_stats.edit', $stat->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('wan_stats.destroy', $stat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>


                        </table>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
@endsection
