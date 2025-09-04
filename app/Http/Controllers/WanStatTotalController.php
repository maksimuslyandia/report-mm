<?php

namespace App\Http\Controllers;

use App\Models\WanMetaData;
use App\Models\WanStatTotal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use InfluxDB2\Client;
use Illuminate\Support\Facades\Http;

class WanStatTotalController extends Controller
{
    public function index()
    {
        $wanStats = WanStatTotal::orderBy('id', 'desc')->with('metaData')->get();

        return view('wan_stats.index', compact('wanStats'));
    }


    public function create()
    {
        return view('wan_stats.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'link_name' => 'required|string',
            'link_type' => 'required|string',
            'region' => 'required|string',
            'bandwidth_bits' => 'required|numeric',
            'traffic_in' => 'required|numeric',
            'traffic_out' => 'required|numeric',
            'q_95_in' => 'required|numeric',
            'q_95_out' => 'required|numeric',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',

            // meta data
            'airport_code' => 'nullable|string',
            'isp_type' => 'nullable|string',
            'wan_stat_total_id' => 'required|exists:wan_stat_totals,id',
            'is_ibo' => 'sometimes|boolean',
        ]);

        WanStatTotal::create($request->all());

        // create WAN Stat
        $wanStat = WanStatTotal::create($validated);

        if ($request->filled('airport_code') || $request->filled('isp_type')) {
            $wanStat->metaData()->create([
                'airport_code' => $request->airport_code,
                'isp_type' => $request->isp_type,
            ]);
        }


        return redirect()->route('wan_stats.index')
            ->with('success', 'WAN Stat Total created successfully.');
    }

    public function show(WanStatTotal $wan_stat)
    {
        return view('wan_stats.show', compact('wan_stat'));
    }

    public function edit(WanStatTotal $wan_stat)
    {
        return view('wan_stats.edit', compact('wan_stat'));
    }

    public function update(Request $request, WanStatTotal $wanStatTotal)
    {

        $validated = $request->validate([
            'link_name' => 'required|string',
            'link_type' => 'required|string',
            'region' => 'required|string',
            'bandwidth_bits' => 'required|numeric',
            'traffic_in' => 'required|numeric',
            'traffic_out' => 'required|numeric',
            'q_95_in' => 'required|numeric',
            'q_95_out' => 'required|numeric',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',

            'airport_code' => 'nullable|string',
            'isp_type' => 'nullable|string',
            'is_ibo' => 'sometimes|boolean',
            'wan_stat_total_id' => 'required|numeric',
        ]);
        $wanStatTotal = WanStatTotal::findOrFail($validated['wan_stat_total_id']);

        // Update WAN Statd
        $wanStatTotal->update($validated);

        // Update or create meta data
        $wanStatTotal->metaData()->updateOrCreate(
            ['wan_stat_total_id' => $request->wan_stat_total_id],
            [
                'airport_code' => $request->airport_code,
                'isp_type' => $request->isp_type,
                'wan_stat_total_id' => $request->wan_stat_total_id,
                'is_ibo' => $request->has('is_ibo') ? true : false,
            ]
        );

        return redirect()->route('wan_stats.index')
            ->with('success', 'WAN Stat Total with metadata updated successfully.');
    }

    public function destroy(WanStatTotal $wan_stat): \Illuminate\Http\RedirectResponse
    {
        $wan_stat->delete();

        return redirect()->route('wan_stats.index')
            ->with('success', 'WAN Stat Total deleted successfully.');
    }

    public function exportCsv()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth()->format('Y-m-d H:i:s');
        $monthStart = \Carbon\Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
//dd($monthStart);
        $stats = WanStatTotal::where('start_datetime', $monthStart)->get();
        //  dd($stats);
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=wan_stats_{$now->format('Y_m')}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'link_name', 'link_type', 'region', 'bandwidth_bits',
            'traffic_in', 'traffic_out', 'q_95_in', 'q_95_out',
            'start_datetime', 'end_datetime'
        ];

        $callback = function () use ($stats, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stats as $row) {
                fputcsv($file, [
                    $row->link_name,
                    $row->link_type,
                    $row->region,
                    $row->bandwidth_bits,
                    $row->traffic_in,
                    $row->traffic_out,
                    $row->q_95_in,
                    $row->q_95_out,
                    $row->start_datetime,
                    $row->end_datetime,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function exportTotalsCsv()
    {
        $now = Carbon::now();
        $monthStart = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');

        // Подгружаем metaData
        $stats = WanStatTotal::with('metaData')->where('start_datetime', $monthStart)->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=wan_stats_{$now->format('Y_m')}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Добавляем новые колонки
        $columns = [
            'airport_code',
            'isp_type',
            'isp',
//            'link_name',
//            'link_type',
//            'region',
//            'bandwidth_bits',
            'traffic_in',
            'traffic_out',
            'q_95_in',
            'q_95_out',
//            'start_datetime', 'end_datetime',
//            'is_ibo' // новые колонки
        ];

        $callback = function () use ($stats, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stats as $row) {
                fputcsv($file, [
                    $row->metaData->airport_code ?? '',
                    $row->metaData->isp_type ?? '',
                    $row->metaData->isp ?? '',
//                    $row->link_name,
//                    $row->link_type,
//                    $row->region,
//                    $row->bandwidth_bits,
                    $row->traffic_in,
                    $row->traffic_out,
                    $row->q_95_in,
                    $row->q_95_out,
//                    $row->start_datetime,
//                    $row->end_datetime,

//                    $row->metaData->is_ibo ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function exportToInflux()
    {

        $now = Carbon::now();
        $monthStart = $now->copy()->subMonth()->startOfMonth();

        // Get all records for the previous month
        $stats = WanStatTotal::where('start_datetime', $monthStart)->get();

        if ($stats->isEmpty()) {
            return response()->json(['message' => 'No records found for previous month.'], 404);
        }

        $client = new Client([
            "url" => 'http://localhost:8086',
            "token" => '7dRogxWiGdluSbNgTybq8ju_RDHN2DrQ1il8j0mhGKgav_vHczhFBKka7A76KGEZX8LTcWEziNQOqQiXbuW-5Q==',
            "org" => 'wbg',
            "bucket" => 'wan_stats',
            "precision" => "s", // timestamps in seconds
        ]);

        $writeApi = $client->createWriteApi();

        foreach ($stats as $record) {
            $point = \InfluxDB2\Point::measurement('wan_stats')
                ->addTag('link_name', $record->link_name)
                ->addTag('link_type', $record->link_type)
                ->addTag('region', $record->region)
                ->addField('bandwidth_bits', $record->bandwidth_bits)
                ->addField('traffic_in', $record->traffic_in)
                ->addField('traffic_out', $record->traffic_out)
                ->addField('q_95_in', $record->q_95_in)
                ->addField('q_95_out', $record->q_95_out)
                ->time($record->start_datetime->timestamp); // seconds

            $points[] = $point; // ✅ add point to array
        }

        // Write all points in batch
        $writeApi->write($points);

        $client->close();

        return response()->json([
            'message' => 'Records written to InfluxDB successfully.',
            'count' => count($points)
        ]);
    }


    public function addMetaData()
    {
        $stats = WanStatTotal::with(['pool.device'])->get();

        foreach ($stats as $stat) {
            if ($stat->pool && $stat->pool->device && $stat->pool->device->hostname) {
                $hostname = $stat->pool->device->hostname;

                // skip first 2 chars, take next 3
                //                if in link_name has HQ then airport code set as HQ
                $airportCode = strtoupper(substr($hostname, 2, 3));


                // Determine ISP type based on link_name
                if (str_contains($stat->link_name, 'IFC-ISP2')) {
                    $ispType = 'IFC';
                    $isp = 'ISP-b';
                    $isIbo = 1;
                }elseif (str_contains($stat->link_name, 'IFC-ISP')) {
                    $ispType = 'IFC';
                    $isp = 'ISP-a';
                    $isIbo = 1;
                } elseif (str_contains($stat->link_name, 'IFC-INTERNET2')) {
                    $ispType = 'IFC';
                    $isp = 'ISP-b';
                    $isIbo = 1;
                } elseif (str_contains($stat->link_name, 'ISP2')) {
                    $ispType = 'IBRD';
                    $isp = 'ISP-b';
                    $isIbo = 1;
                } elseif (str_contains($stat->link_name, 'ISP')) {
                    $ispType = 'IBRD';
                    $isp = 'ISP-a';
                    $isIbo = 1;
                }elseif (str_contains($stat->link_name, 'VSAT')) {
                    $ispType = 'VSAT';
                    $isp = 'Satellite';
                    $isIbo = 0;
                }elseif (str_contains($stat->link_name, 'IPLC')) {
                    $ispType = 'IPLC';
                    $isp = 'IPLC';
                    $isIbo = 0;
                } elseif (str_contains($stat->link_name, 'HQ') && str_contains($stat->link_name, 'MPLS')) {
                    $ispType = 'MPLS';
                    $isp = 'HQ';
                    $isIbo = 0;
                } elseif (str_contains($stat->link_name, 'MPLS')) {
                    $ispType = 'MPLS';
                    $isp = 'MPLS';
                    $isIbo = 0;
                } else {
                    $ispType = $stat->link_type ?? 'UNKNOWN';
                    $isp = $stat->link_type ?? 'UNKNOWN';
                    $isIbo = 0;
                }

                // insert or update to avoid duplicates
                WanMetaData::updateOrCreate(
                    ['wan_stat_total_id' => $stat->id],
                    [
                        'airport_code' => $airportCode,
                        'isp_type' => $ispType,
                        'is_ibo' => $isIbo,
                        'isp' => $isp,
                    ]
                );
            }
        }

        return response()->json(['message' => 'Meta data generated successfully']);
    }
    public function addPfr()
    {

        $pfrHostnames = [
            'crbgw0','crbjs0','crcoo0','crdil0','crfih0','crgbe0','cricn0',
            'crjib0','crles0','crlfw0','crmru0','crndj0','crnim0','crnkc0',
            'crpry0','crrob0','crruh0','crtgu0','crtnr0','rmdel1','rmhkg1',
        ];

        $start = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $end   = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');

        foreach ($pfrHostnames as $hostname) {
            try {
                $response = Http::get("http://report-mm.worldbank.org/export-sum-norm", [
                    'hostname1' => $hostname,
                    'interface1_isp_a' => 'Tu9',
                ]);

                if ($response->failed()) {
                    \Log::error("Failed to fetch data for {$hostname}");
                    continue;
                }

                $data = $response->json();
                if (!$data) {
                    \Log::error("Invalid JSON response for {$hostname}");
                    continue;
                }

                $airport_code = strtoupper(substr($hostname, 2, 3));
                $wanStat = WanStatTotal::with('metaData')
                    ->whereHas('metaData', function ($query) use ($airport_code) {
                        $query->where('airport_code', $airport_code);
                    })
                    ->orderBy('id', 'asc') // first based on ID
                    ->first();
                $region = $wanStat->region;


//select from WAN Stat Total where aircode to get region
                // Create or update WAN Stat Total
                $wanStatTotal = WanStatTotal::updateOrCreate(
                    [
                        'link_name'      => $hostname,
                        'start_datetime' => $start,
                        'end_datetime'   => $end,
                    ],
                    [
                        'link_type'      => 'ISP-PFR',
                        'region'         => $region ?? "NULL",
                        'bandwidth_bits' => 0,
                        'traffic_in'     => (int) ($data['total_sum_in'] ?? 0),
                        'traffic_out'    => (int) ($data['total_sum_out'] ?? 0),
                        'q_95_in'        => (int) ($data['quantile_95_in'] ?? 0),
                        'q_95_out'       => (int) ($data['quantile_95_out'] ?? 0),
                    ]
                );

                // Create or update WAN Meta Data linked to it
                $wanStatTotal->metaData()->updateOrCreate(
                    ['wan_stat_total_id' => $wanStatTotal->id],
                    [
                        'airport_code' => $airport_code,
                        'isp'          => null,
                        'isp_type'     => 'PFR',
                        'is_ibo'       => false,
                    ]
                );

            } catch (\Exception $e) {
                \Log::error("Exception for {$hostname}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'PFR stats + metadata imported/updated successfully']);
    }
    public function addIBO()
    {
        $pools = [
            'IBO' => [
                'hostnames' => [
                    'pazag',
                    'paank',
                    'pamaa0-rmz',
                    'pasal',
                    'patse',
                    'patas',
                    'palbv',
                    'padla1',
                    'pamga',
                    'pasin',
                    'pahre',
                    'palun',
                    'paskp',
                    'pajib',
                    'patrw',
                    'pasao1',
                    'padod',
                    'pabgw',
                    'papbh',
                    'pakgl',
                    'pales',
                    'pabgf',
                    'pakwi',
                    'paprn',
                    'panim',
                    'pasjo',
                    'pawbg',
                    'pasjj',
                    'pajnb1',
                    'patbu',
                    'pacai1',
                    'panbo',
                    'pabog',
                    'patbs',
                    'paapw',
                    'patia',
                    'pabjl',
                    'pabjm',
                    'pabeg1',
                    'padac1',
                    'pabjs',
                    'pajkt',
                    'pabuh',
                    'pampm',
                    'papry',
                    'pasdq1',
                    'paevn',
                    'paktm',
                    'pasof',
                    'paoua',
                    'pabru',
                    'pahkg1',
                    'pampm1',
                    'pagva',
                    'pafih',
                    'pabzv',
                    'pavte',
                    'pabak',
                    'pamaa',
                    'pafru',
                    'pakul',
                    'pauln',
                    'pasyd',
                    'padkr1',
                    'pasgn1',
                    'pacoo',
                    'pamnl',
                    'pawaw',
                    'pargn',
                    'pafun',
                    'paist1',
                    'paalg',
                    'pahnd',
                    'pabue',
                    'padil',
                    'padxb1',
                    'pakbl',
                    'pacdg',
                    'parob',
                    'pandj',
                    'palfw',
                    'paasb',
                    'paabv',
                    'parom',
                    'patgd',
                    'pahah',
                    'pamru',
                    'papni',
                    'paabj',
                    'papnh',
                    'paicn',
                    'patgu',
                    'paasu',
                    'paisb',
                    'palad',
                    'pabko',
                    'pahir',
                    'pageo',
                    'pabkk',
                    'pahan',
                    'padkr',
                    'pamvd',
                    'padyu',
                    'patnr',
                    'payao',
                    'padel-55le',
                    'pabsb',
                    'pajub',
                    'paruh',
                    'palim',
                    'pacmb',
                    'pascl',
                    'parai',
                    'padel1',
                    'pafna',
                    'paebb',
                    'patun',
                    'pagua',
                    'pakiv',
                    'palon1',
                    'paadd',
                    'paala',
                    'palos1',
                    'pavie',
                    'pallw',
                    'pakin',
                    'pagbe',
                    'paacc1',
                    'pamaa0-sp',
                    'palpb',
                    'pakhi1',
                    'pankc',
                    'pamex',
                    'padac',
                    'paauh',
                    'paber',
                    'pamex1',
                    'pabey',
                    'padar',
                    'pakbp',
                    'papty',
                    'packy',
                    'pasuv',
                    'papom',
                    'pamow1',
                    'pabom1',
                    'paamm',

                ], // add full list
                'link_type' => 'ISP-IBO',
                'isp_type' => 'IBO',
                'interfaces' => ['ethernet1/1', 'ethernet1/8'], // two interfaces
            ],
            // add more pools here
        ];

        $start = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');

        foreach ($pools as $poolName => $pool) {
            foreach ($pool['hostnames'] as $hostname) {
                try {
                    // Prepare query parameters for API call
                    $query = ['hostname1' => $hostname];
                    foreach ($pool['interfaces'] as $index => $iface) {
                        $query['interface' . ($index + 1) . '_isp_a'] = $iface;
                    }

                    $response = Http::get("http://report-mm.worldbank.org/export-sum-norm", $query);

                    if ($response->failed()) {
                        \Log::error("Failed to fetch data for {$hostname}");
                        continue;
                    }

                    $data = $response->json();
                    if (!$data) {
                        \Log::error("Invalid JSON response for {$hostname}");
                        continue;
                    }

                    $airport_code = strtoupper(substr($hostname, 2, 3));

                    $wanStat = WanStatTotal::with('metaData')
                        ->whereHas('metaData', function ($query) use ($airport_code) {
                            $query->where('airport_code', $airport_code);
                        })
                        ->orderBy('id', 'asc')
                        ->first();

                    $region = $wanStat->region ?? null;

                    // Update or create WAN Stat Total
                    $wanStatTotal = WanStatTotal::updateOrCreate(
                        [
                            'link_name' => $hostname,
                            'start_datetime' => $start,
                            'end_datetime' => $end,
                        ],
                        [
                            'link_type' => $pool['link_type'],
                            'region' => $region ?? "NULL",
                            'bandwidth_bits' => 0,
                            'traffic_in' => (int)($data['total_sum_in'] ?? 0),
                            'traffic_out' => (int)($data['total_sum_out'] ?? 0),
                            'q_95_in' => (int)($data['quantile_95_in'] ?? 0),
                            'q_95_out' => (int)($data['quantile_95_out'] ?? 0),
                        ]
                    );

                    // Update or create metadata
                    $wanStatTotal->metaData()->updateOrCreate(
                        ['wan_stat_total_id' => $wanStatTotal->id],
                        [
                            'airport_code' => $airport_code,
                            'isp' => null,
                            'isp_type' => $pool['isp_type'],
                            'is_ibo' => $pool['isp_type'] === 'IBO',
                        ]
                    );

                } catch (\Exception $e) {
                    \Log::error("Exception for {$hostname}: " . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'WAN stats + metadata imported/updated successfully']);


    }
}
