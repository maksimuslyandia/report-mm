<?php
namespace App\Http\Controllers;

use App\Models\WanStatTotal;
use Illuminate\Http\Request;

class WanStatTotalController extends Controller
{
    public function index()
    {
        $wanStats = WanStatTotal::all();
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

    public function show(WanStatTotal $wanStatTotal): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('wan_stats.show', compact('wanStatTotal'));
    }

    public function edit(WanStatTotal $wanStatTotal): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('wan_stats.edit', compact('wanStatTotal'));
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

    public function destroy(WanStatTotal $wanStatTotal): \Illuminate\Http\RedirectResponse
    {
        $wanStatTotal->delete();

        return redirect()->route('wan_stats.index')
            ->with('success','WAN Stat Total deleted successfully.');
    }
}
