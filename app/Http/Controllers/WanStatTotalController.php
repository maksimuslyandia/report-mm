<?php
namespace App\Http\Controllers;

use App\Models\WanStatTotal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use InfluxDB2\Client;

class WanStatTotalController extends Controller
{
    public function index()
    {
        $wanStats = WanStatTotal::orderBy('id', 'desc')->get();
        return view('wan_stats.index', compact('wanStats'));
    }


    public function create()
    {
        return view('wan_stats.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
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
        ]);

        WanStatTotal::create($request->all());

        return redirect()->route('wan_stats.index')
            ->with('success','WAN Stat Total created successfully.');
    }

    public function show(WanStatTotal $wan_stat)
    {
        return view('wan_stats.show', compact('wan_stat'));
    }

    public function edit(WanStatTotal $wan_stat)
    {
        return view('wan_stats.edit', compact('wan_stat'));
    }

    public function update(Request $request, WanStatTotal $wanStatTotal): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
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
        ]);

        $wanStatTotal->update($request->all());

        return redirect()->route('wan_stats.index')
            ->with('success','WAN Stat Total updated successfully.');
    }

    public function destroy(WanStatTotal $wan_stat): \Illuminate\Http\RedirectResponse
    {
        $wan_stat->delete();

        return redirect()->route('wan_stats.index')
            ->with('success','WAN Stat Total deleted successfully.');
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
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=wan_stats_{$now->format('Y_m')}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'link_name', 'link_type', 'region', 'bandwidth_bits',
            'traffic_in', 'traffic_out', 'q_95_in', 'q_95_out',
            'start_datetime', 'end_datetime'
        ];

        $callback = function() use ($stats, $columns) {
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
}
