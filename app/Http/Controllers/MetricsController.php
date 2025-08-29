<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;
use InfluxDB2\Model\FluxTable;
use Illuminate\Http\JsonResponse;
use InfluxDB2\Model\WritePrecision;

class MetricsController extends Controller
{

    protected Client $client;
    protected string $bucket;
    protected string $org;
    protected string $token;

    public function __construct()
    {
        $this->bucket = env('INFLUXDB_BUCKET'); // Your InfluxDB bucket name
        $this->org = env('INFLUXDB_ORG'); // Your InfluxDB organization
        $this->token = env('INFLUXDB_TOKEN'); // Your InfluxDB token

        // Initialize the InfluxDB client with a higher timeout (e.g., 30 seconds)
        $this->client = new Client([
            'url' => env('INFLUXDB_URL'), // Your InfluxDB instance URL
            'token' => $this->token,
            'bucket' => $this->bucket,
            "org" => "wbg",
            "debug" => false,
            'timeout' => 30, // Increase timeout to 30 seconds
        ]);
    }


    public function getMetrics_good(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required',
            'interface' => 'required',
        ]);

        $hostname = $request->hostname;
        $interface = $request->interface;
        $density = $request->density ?? '1h';
        $daysParam = $request->days ?? 'month';

        $this->queryApi = $this->client->createQueryApi();

        $timezone = new \DateTimeZone('America/New_York');

// Handle time range
        if (is_numeric($daysParam)) {
            // Numeric value: use it as number of past days
            $days = (int) $daysParam;

            $startDate = new \DateTime("-{$days} days", $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('yesterday', $timezone);
            $stopDate->setTime(23, 59, 59);
        } else {
            // Fallback to last month if not numeric (e.g., "month")
            $startDate = new \DateTime('first day of last month', $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('last day of last month', $timezone);
            $stopDate->setTime(23, 59, 59);
        }

// Convert to UTC
        $startDate->setTimezone(new \DateTimeZone('UTC'));
        $stopDate->setTimezone(new \DateTimeZone('UTC'));
//        dd($startDate)

// Format for InfluxDB (ISO 8601 with UTC "Z" suffix)
        $startFormatted = $startDate->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stopDate->format('Y-m-d\TH:i:s\Z');
//        $hostname = 'crsof0';
//        $interface = 'Gi0/0/1';
        $tables = $this->queryApi->query(
            '
            import "sampledata"
            import "strings"
            from(bucket: "snmp_1")
              |> range(start: time(v: ' . $startFormatted . '), stop: time(v: ' . $stopFormatted . '))
              |> filter(fn: (r) => r["_measurement"] == "pysnmp")
              |> filter(fn: (r) => r["device_name"] == "' . $hostname . '")
              |> filter(fn: (r) => r["ifName"] == "' . $interface . '")
              |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
              |> derivative(unit:1s, nonNegative: true)
              |> map(fn: (r) => ({ r with _value: float(v:r._value) * 8.00 }))
              |> filter(fn: (r) => r._value < 139999999)
              |> aggregateWindow(every: 1h, fn: mean, createEmpty: true)
              //|> fill(usePrevious: true) // Replace null (formerly 0) with previous value
              |> fill(value: 0.00)
              |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
               ');

        $data = [];

// Flatten the FluxTable objects into plain arrays
//        foreach ($tables as $table) {
//            foreach ($table->records as $record) {
//
//                $data[] = [
//                    $record->values['_time'],
//                    $record->values['device_name'],
//                    $record->values['ifAlias'],
//                    $record->values['ifDescr'],
//                    $record->values['ifName'],
//                    $record->values['ifHCInOctets'],
//                    $record->values['ifHCOutOctets'],
//                ];
//            }
//        }
//dd($tables);
        return view('welcome', [
                'tables' => $tables,
                'json' =>  response()->json($data)
            ]
        );
    }
    public function getMetrics(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required',
            'interface' => 'required',
        ]);

        $hostname = $request->hostname;
        $interface = $request->interface;
        $density = $request->density ?? '1h';
        $daysParam = $request->days ?? 'month';

        $this->queryApi = $this->client->createQueryApi();

        $timezone = new \DateTimeZone('America/New_York');

// Handle time range
        if (is_numeric($daysParam)) {
            // Numeric value: use it as number of past days
            $days = (int) $daysParam;

            $startDate = new \DateTime("-{$days} days", $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('yesterday', $timezone);
            $stopDate->setTime(23, 59, 59);
        } else {
            // Fallback to last month if not numeric (e.g., "month")
            $startDate = new \DateTime('first day of last month', $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('last day of last month', $timezone);
            $stopDate->setTime(23, 59, 59);
        }

// Convert to UTC
        $startDate->setTimezone(new \DateTimeZone('UTC'));
        $stopDate->setTimezone(new \DateTimeZone('UTC'));
//        dd($startDate)

// Format for InfluxDB (ISO 8601 with UTC "Z" suffix)
        $startFormatted = $startDate->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stopDate->format('Y-m-d\TH:i:s\Z');
//        $hostname = 'crsof0';
//        $interface = 'Gi0/0/1';
        $tables = $this->queryApi->query(
            '
            import "sampledata"
            import "strings"
            from(bucket: "snmp_1")
              |> range(start: time(v: ' . $startFormatted . '), stop: time(v: ' . $stopFormatted . '))
              |> filter(fn: (r) => r["_measurement"] == "pysnmp")
              |> filter(fn: (r) => r["device_name"] == "' . $hostname . '")
              |> filter(fn: (r) => r["ifName"] == "' . $interface . '")
              |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
              |> derivative(unit:1s, nonNegative: true)
              |> map(fn: (r) => ({ r with _value: float(v:r._value) * 8.00 }))
              |> filter(fn: (r) => r._value < 99999999)
              |> aggregateWindow(every: 1h, fn: mean, createEmpty: true)
              //|> fill(usePrevious: true) // Replace null (formerly 0) with previous value
              |> fill(value: 0.00)
              |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
               ');

        $data = [];

// Flatten the FluxTable objects into plain arrays
//        foreach ($tables as $table) {
//            foreach ($table->records as $record) {
//
//                $data[] = [
//                    $record->values['_time'],
//                    $record->values['device_name'],
//                    $record->values['ifAlias'],
//                    $record->values['ifDescr'],
//                    $record->values['ifName'],
//                    $record->values['ifHCInOctets'],
//                    $record->values['ifHCOutOctets'],
//                ];
//            }
//        }
//dd($tables);
        return view('welcome', [
                'tables' => $tables,
                'json' =>  response()->json($data)
            ]
        );
    }

    public function getMetrics_origin(Request $request)
    {
        $validated = $request->validate([
            'hostname' => 'required',
            'interface' => 'required',
        ]);

        $hostname = $request->hostname;
        $interface = $request->interface;
        $density = $request->density ?? '1h';
        $daysParam = $request->days ?? 'month';

        $this->queryApi = $this->client->createQueryApi();

        $timezone = new \DateTimeZone('America/New_York');

// Handle time range
        if (is_numeric($daysParam)) {
            // Numeric value: use it as number of past days
            $days = (int) $daysParam;

            $startDate = new \DateTime("-{$days} days", $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('yesterday', $timezone);
            $stopDate->setTime(23, 59, 59);
        } else {
            // Fallback to last month if not numeric (e.g., "month")
            $startDate = new \DateTime('first day of last month', $timezone);
            $startDate->setTime(0, 0, 0);

            $stopDate = new \DateTime('last day of last month', $timezone);
            $stopDate->setTime(23, 59, 59);
        }

// Convert to UTC
        $startDate->setTimezone(new \DateTimeZone('UTC'));
        $stopDate->setTimezone(new \DateTimeZone('UTC'));
//        dd($startDate)

// Format for InfluxDB (ISO 8601 with UTC "Z" suffix)
        $startFormatted = $startDate->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stopDate->format('Y-m-d\TH:i:s\Z');
//        $hostname = 'crsof0';
//        $interface = 'Gi0/0/1';
        $tables = $this->queryApi->query(
            '
            import "sampledata"
            import "strings"
            from(bucket: "snmp_1")
              |> range(start: time(v: ' . $startFormatted . '), stop: time(v: ' . $stopFormatted . '))
              |> filter(fn: (r) => r["_measurement"] == "pysnmp")
              |> filter(fn: (r) => r["device_name"] == "' . $hostname . '")
              |> filter(fn: (r) => r["ifName"] == "' . $interface . '")
              |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
              |> derivative(unit:1s, nonNegative: true)
              |> map(fn: (r) => ({ r with _value: float(v:r._value) * 8.00 }))
              |> filter(fn: (r) => r._value < 9999999)
              |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
              |> fill(usePrevious: true) // Replace null (formerly 0) with previous value
              |> fill(value: 0.00)
              |> pivot(rowKey: ["_time"], columnKey: ["_field"], valueColumn: "_value")
               ');

        $data = [];

// Flatten the FluxTable objects into plain arrays
//        foreach ($tables as $table) {
//            foreach ($table->records as $record) {
//
//                $data[] = [
//                    $record->values['_time'],
//                    $record->values['device_name'],
//                    $record->values['ifAlias'],
//                    $record->values['ifDescr'],
//                    $record->values['ifName'],
//                    $record->values['ifHCInOctets'],
//                    $record->values['ifHCOutOctets'],
//                ];
//            }
//        }
//dd($tables);
        return view('welcome', [
                'tables' => $tables,
                'json' =>  response()->json($data)
            ]
        );
    }


}
