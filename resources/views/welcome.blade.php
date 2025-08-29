{{--@foreach($tables as $table)--}}
{{--    @foreach($table->records as $record)--}}
{{--        {{ \Carbon\Carbon::parse($record->values['_time'])->setTimezone('America/New_York')->format('m/d/Y H:i:s') }},{{$record->values['ifHCInOctets']}},{{$record->values['ifHCOutOctets']}}<br>--}}
{{--    @endforeach--}}

{{--@endforeach--}}
@foreach($tables[0]->records as $record)
    {{ \Carbon\Carbon::parse($record->values['_time'])->setTimezone('America/New_York')->format('m/d/Y H:i:s') }},{{$record->values['ifHCInOctets']}},{{$record->values['ifHCOutOctets']}}<br>
@endforeach
