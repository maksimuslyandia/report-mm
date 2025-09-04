<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Link Name</label>
        <input type="text" name="link_name" value="{{ old('link_name', $wanStatTotal->link_name ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Link Type</label>
        <input type="text" name="link_type" value="{{ old('link_type', $wanStatTotal->link_type ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Region</label>
        <input type="text" name="region" value="{{ old('region', $wanStatTotal->region ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Bandwidth (bits)</label>
        <input type="number" name="bandwidth_bits" value="{{ old('bandwidth_bits', $wanStatTotal->bandwidth_bits ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Traffic In</label>
        <input type="number" name="traffic_in" value="{{ old('traffic_in', $wanStatTotal->traffic_in ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Traffic Out</label>
        <input type="number" name="traffic_out" value="{{ old('traffic_out', $wanStatTotal->traffic_out ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">95% In</label>
        <input type="number" name="q_95_in" value="{{ old('q_95_in', $wanStatTotal->q_95_in ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">95% Out</label>
        <input type="number" name="q_95_out" value="{{ old('q_95_out', $wanStatTotal->q_95_out ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">Start Datetime</label>
        <input type="datetime-local" name="start_datetime"
               value="{{ old('start_datetime', isset($wanStatTotal) ? \Carbon\Carbon::parse($wanStatTotal->start_datetime)->format('Y-m-d\TH:i') : '') }}"
               class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">End Datetime</label>
        <input type="datetime-local" name="end_datetime"
               value="{{ old('end_datetime', isset($wanStatTotal) ? \Carbon\Carbon::parse($wanStatTotal->end_datetime)->format('Y-m-d\TH:i') : '') }}"
               class="form-control">
    </div>
    <div class="col-md-6">
        <label>Airport Code</label>
        <input type="text" name="airport_code"
               value="{{ old('airport_code', $wan_stat->metaData->airport_code ?? '') }}"
               class="form-control">
    </div>
    <div class="col-md-6">
        <label>ISP Type</label>
        <input type="text" name="isp_type"
               value="{{ old('isp_type', $wan_stat->metaData->isp_type ?? '') }}"
               class="form-control">
    </div>


    <div class="col-md-6">
        <input type="checkbox"
               class="form-check-input"
               id="is_ibo"
               name="is_ibo"
               value="1"
               @if(isset($metaData) && $metaData->is_ibo) checked @endif>
        <label class="form-check-label" for="is_ibo">Is IBO</label>
    </div>


</div>
