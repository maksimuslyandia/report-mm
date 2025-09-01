<?php

namespace App\Http\Controllers;

use App\Models\WanStatTotal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use InfluxDB2\Client;
use InfluxDB2\Model\FluxTable;
use Illuminate\Http\JsonResponse;
use InfluxDB2\Model\WritePrecision;
use App\Models\Pool;
use App\Models\Device;
use App\Models\DeviceInterface;


class SumMetricsController extends Controller
{
    protected array $predefinedMetrics = [
        'rmdac1|Gi0/0/1|rhdac1|Gi0/0/1|padac1|ethernet1/1' => [
            'total_sum_in' => 7993189260601.55,
            'total_sum_out' => 6150140842241.33,
            'quantile_95_in' => 14523574.24,
            'quantile_95_out' => 12249309.03,
        ],
        'rmsao1|Gi0/0/1|rhsao1|Gi0/0/1|pasao1|ethernet1/1' => [
            'total_sum_in' => 19511247137056.9,
            'total_sum_out' => 15582848751010.42,
            'quantile_95_in' => 42993153.3,
            'quantile_95_out' => 36563710.44,
        ],
        'rmdla1|Gi0/0/1|rhdla1|Gi0/0/1|padla1|ethernet1/1' => [
            'total_sum_in' => 3968095119622.71,
            'total_sum_out' => 2883736261292.42,
            'quantile_95_in' => 9223402.87,
            'quantile_95_out' => 5896977.01,
        ],
        'rmdla1|Gi0/0/2|rhdla1|Gi0/0/2|padla1|ethernet1/8' => [
            'total_sum_in' => 7681208661630.38,
            'total_sum_out' => 1500517903461.18,
            'quantile_95_in' => 9489261.96,
            'quantile_95_out' => 2008474.17,
        ],
        'rmhkg1|Gi0/0/1|rhhkg1|Gi0/0/1|pahkg1|ethernet1/1' => [
            'total_sum_in' => 61017211675912.79,
            'total_sum_out' => 50932660428092.01,
            'quantile_95_in' => 112055164.41,
            'quantile_95_out' => 10528215.84,
        ],
        'rmsdq1|Gi0/0/1|rhsdq1|Gi0/0/1|pasdq1|ethernet1/1' => [
            'total_sum_in' => 10646874898810.43,
            'total_sum_out' => 6127170720253.64,
            'quantile_95_in' => 21248989.44,
            'quantile_95_out' => 10856647.79,
        ],
        'rmsdq1|Gi0/0/2|rhsdq1|Gi0/0/2|pasdq1|ethernet1/8' => [
            'total_sum_in' => 844797348788.83,
            'total_sum_out' => 268695165540.03,
            'quantile_95_in' => 1128934.93,
            'quantile_95_out' => 402301.69,
        ],
        'rmcai1|Gi0/0/1|rhcai1|Gi0/0/1|pacai1|ethernet1/1' => [
            'total_sum_in' => 20251463764610.44,
            'total_sum_out' => 17142817897294.5,
            'quantile_95_in' => 39184990.08,
            'quantile_95_out' => 36164341.83,
        ],
        'rmacc1|Gi0/0/1|rhacc1|Gi0/0/1|paacc1|ethernet1/1' => [
            'total_sum_in' => 16706019525592.31,
            'total_sum_out' => 1011149689222.62,
            'quantile_95_in' => 33963226.11,
            'quantile_95_out' => 21269400.74,
        ],
        'rmdel1|Gi0/0/1|rhdel1|Gi0/0/1|padel1|ethernet1/1' => [
            'total_sum_in' => 31978346800164.49,
            'total_sum_out' => 11779387200659.53,
            'quantile_95_in' => 43246884.28,
            'quantile_95_out' => 20308700.53,
        ],
        'rmmex1|Gi0/0/1|rhmex1|Gi0/0/1|pamex1|ethernet1/1' => [
            'total_sum_in' => 28847544603572.87,
            'total_sum_out' => 16242713032634.65,
            'quantile_95_in' => 50285753.34,
            'quantile_95_out' => 33055844.86,
        ],
        'rmmpm1|Gi0/0/2|rhmpm1|Gi0/0/2|pampm1|ethernet1/8' => [
            'total_sum_in' => 4925572543840.88,
            'total_sum_out' => 1057028491911.15,
            'quantile_95_in' => 6593582.38,
            'quantile_95_out' => 1583067.33,
        ],
        'rmlos1|Gi0/0/1|rhlos1|Gi0/0/1|palos1|ethernet1/1' => [
            'total_sum_in' => 10843125327333.48,
            'total_sum_out' => 7482785749615.85,
            'quantile_95_in' => 23618535,
            'quantile_95_out' => 16653729,
        ],
        'rmkhi1|Gi0/0/1|rhkhi1|Gi0/0/1|pakhi1|ethernet1/1' => [
            'total_sum_in' => 1536934044337.8,
            'total_sum_out' => 2022489285173.69,
            'quantile_95_in' => 2445028,
            'quantile_95_out' => 3110058,
        ],
        'rmmow1|Gi0/0/1|rhmow1|Gi0/0/1|pamow1|ethernet1/1' => [
            'total_sum_in' => 1328968661145.18,
            'total_sum_out' => 765507978843.62,
            'quantile_95_in' => 665163,
            'quantile_95_out' => 888935,
        ],
        'rmmow1|Gi0/0/2|rhmow1|Gi0/0/2|pamow1|ethernet1/8' => [
            'total_sum_in' => 1491024960100.34,
            'total_sum_out' => 500938239336.92,
            'quantile_95_in' => 2674485.23,
            'quantile_95_out' => 462248.88,
        ],
        'rmdkr1|Gi0/0/1|rhdkr1|Gi0/0/1|padkr1|ethernet1/1' => [
            'total_sum_in' => 12066765267909.21,
            'total_sum_out' => 8071713387871.04,
            'quantile_95_in' => 22338786.37,
            'quantile_95_out' => 14841663.26,
        ],
        'rmbeg1|Gi0/0/1|rhbeg1|pabeg1|ethernet1/1' => [
            'total_sum_in' => 5986578099145.35,
            'total_sum_out' => 6165323453283.91,
            'quantile_95_in' => 12514220,
            'quantile_95_out' => 11598674,
        ],
        'rmjnb1|Gi0/0/1|rhjnb1|Gi0/0/1|pajnb1|ethernet1/1' => [
            'total_sum_in' => 14030479049675.24,
            'total_sum_out' => 10934390024988.23,
            'quantile_95_in' => 29015773.61,
            'quantile_95_out' => 23621464.21,
        ],
        'rmist1|Gi0/0/1|rhist1|Gi0/0/1|paist1|ethernet1/1' => [
            'total_sum_in' => 17381905347086.1,
            'total_sum_out' => 15523423427777.41,
            'quantile_95_in' => 38387938.33,
            'quantile_95_out' => 31741224.25,
        ],
        'rmdxb1|Gi0/0/1|rhdxb1|Gi0/0/1|padxb1|ethernet1/1' => [
            'total_sum_in' => 10297711783517.92,
            'total_sum_out' => 7197724935601.34,
            'quantile_95_in' => 20096879.25,
            'quantile_95_out' => 15806012.61,
        ],
        'rmlon1|Gi0/0/1|rhlon1|Gi0/0/1|palon1|ethernet1/1' => [
            'total_sum_in' => 15657003360712.65,
            'total_sum_out' => 12540584578656.47,
            'quantile_95_in' => 32709210,
            'quantile_95_out' => 24012682,
        ],
        'rmsgn1|Gi0/0/1|rhsgn1|Gi0/0/1|pasgn1|ethernet1/1' => [
            'total_sum_in' => 4665377707645.29,
            'total_sum_out' => 3911951271335.2,
            'quantile_95_in' => 8121740.74,
            'quantile_95_out' => 7535086,
        ],
    ];
    protected array $predefinedMetricsMay = [
        'rmbom1|Gi0/0/2|rhbom1|Gi0/0/2|pabom1|ethernet1/8' => ['total_sum_in' => 21249560398.69, 'total_sum_out' => 12725098070.48, 'quantile_95_in' => 8188.35, 'quantile_95_out' => 4966.92],
        'crkiv0|Gi0/0/2|cskiv0|Gi0/0/2|pakiv0|ethernet1/8' => ['total_sum_in' => 15115255895.54, 'total_sum_out' => 13774794941.13, 'quantile_95_in' => 6065.46, 'quantile_95_out' => 5194.77],
        'crtgu0|Gi0/0/2|cstgu0|Gi0/0/2' => ['total_sum_in' => 4154117447.84, 'total_sum_out' => 1709672645.16, 'quantile_95_in' => 1761.80, 'quantile_95_out' => 701.61],
        'rmmex1|Gi0/0/2|rhmex1|Gi0/0/2' => ['total_sum_in' => 4880615379.82, 'total_sum_out' => 2531696580.54, 'quantile_95_in' => 1891.24, 'quantile_95_out' => 799.69],
        'crtms0|Gi0/0/1|cstms0|Gi0/0/2' => ['total_sum_in' => 10421846885.54, 'total_sum_out' => 7667376974.06, 'quantile_95_in' => 3872.44, 'quantile_95_out' => 2905.98],
        'rmfra1|Gi0/0/0|rhfra1|Gi0/0/2' => ['total_sum_in' => 1636794648918.29, 'total_sum_out' => 999234796991.97, 'quantile_95_in' => 3302965.20, 'quantile_95_out' => 1969024.37],
        'rmdel1|Gi0/0/2|rhdel1|Gi0/0/2' => ['total_sum_in' => 2274686515571.73, 'total_sum_out' => 3402841632329.16, 'quantile_95_in' => 2654327.53, 'quantile_95_out' => 3144110.30],
        'rmfra1|Gi0/0/1|rhfra1|Gi0/0/2' => ['total_sum_in' => 15096949235.69, 'total_sum_out' => 14099706902.07, 'quantile_95_in' => 5684.71, 'quantile_95_out' => 5247.13],
        'rmhkg1|Gi0/0/2|rhhkg1|Gi0/0/2' => ['total_sum_in' => 1864898761445.83, 'total_sum_out' => 5741422070905.91, 'quantile_95_in' => 2007616.02, 'quantile_95_out' => 4948005.26],
        'rmkhi1|Gi0/0/1|rhkhi1|Gi0/0/1|pakhi1|ethernet1/1' => ['total_sum_in' => 1235114317774.74, 'total_sum_out' => 1592084464116.58, 'quantile_95_in' => 1255556.20, 'quantile_95_out' => 1465111.42],
        'rmmow1|Gi0/0/1|rhmow1|Gi0/0/1|pamow1|ethernet1/1' => ['total_sum_in' => 477547907552.70, 'total_sum_out' => 897892076580.94, 'quantile_95_in' => 249133.11, 'quantile_95_out' => 441937.08],
        'rmdkr1|Gi0/0/1|rhdkr1|Gi0/0/1|padkr1|ethernet1/1' => ['total_sum_in' => 6026361961933.44, 'total_sum_out' => 3735608892085.89, 'quantile_95_in' => 8041552.15, 'quantile_95_out' => 4277110.27],
        'rmbeg1|Gi0/0/1|rhbeg1|Gi0/0/1|pabeg1|ethernet1/1' => ['total_sum_in' => 5143783629297.15, 'total_sum_out' => 5360751244857.61, 'quantile_95_in' => 8535481.58, 'quantile_95_out' => 7458881.75],
//        'crhah0|Gi0/0/2|cshah0|Gi0/0/2' => ['total_sum_in' => 30676643789.79, 'total_sum_out' => 18623850152.45, 'quantile_95_in' => 3349.93, 'quantile_95_out'=> 796.59],
        'rmdla1|Gi0/0/1|rhdla1|Gi0/0/1|padla1|ethernet1/1' => ['total_sum_in' => 3266652616025.27, 'total_sum_out' => 2588493633572.45, 'quantile_95_in' => 5485190.05, 'quantile_95_out' => 3848011.50],
        'rmsao1|Gi0/0/1|rhsao1|Gi0/0/1|pasao1|ethernet1/1' => ['total_sum_in' => 18913200193894.10, 'total_sum_out' => 16001583571954.90, 'quantile_95_in' => 33280059.39, 'quantile_95_out' => 26023394.07],
        'rmlon1|Gi0/0/1|rhlon1|Gi0/0/1|palon1|ethernet1/1' => ['total_sum_in' => 11858575683041.00, 'total_sum_out' => 9896314124136.00, 'quantile_95_in' => 19012746.70, 'quantile_95_out' => 14869658.56],
//        'rmdac1|Gi0/0/1|rhdac1|Gi0/0/1|padac1|ethernet1/1' => ['total_sum_in' => 7654153804689.45, 'total_sum_out' => 6103222965752.05, 'quantile_95_in' => 9248358.59, 'quantile_95_out'=> 8339583.38],
        'rmjnb1|Gi0/0/1|rhjnb1|Gi0/0/1|pajnb1|ethernet1/1' => ['total_sum_in' => 13466590126398.20, 'total_sum_out' => 10415745546396.60, 'quantile_95_in' => 25705358.74, 'quantile_95_out' => 17235847.66],
        'rmsdq1|Gi0/0/1|rhsdq1|Gi0/0/1|pasdq1|ethernet1/1' => ['total_sum_in' => 5598539147770.15, 'total_sum_out' => 4427962606813.49, 'quantile_95_in' => 9560932.61, 'quantile_95_out' => 7084391.25],
        'rmist1|Gi0/0/1|rhist1|Gi0/0/1|paist1|ethernet1/1' => ['total_sum_in' => 16658106834836.70, 'total_sum_out' => 17908734339790.50, 'quantile_95_in' => 29376085.86, 'quantile_95_out' => 27468593.79],
        'rmhkg1|Gi0/0/1|rhhkg1|Gi0/0/1|pahkg1|ethernet1/1' => ['total_sum_in' => 60722318913582.60, 'total_sum_out' => 47546021476208.30, 'quantile_95_in' => 89088983.15, 'quantile_95_out' => 68959847.63],
        'rmlos1|Gi0/0/1|rhlos1|Gi0/0/1|palos1|ethernet1/1' => ['total_sum_in' => 10543744878083.70, 'total_sum_out' => 7679809255738.10, 'quantile_95_in' => 19006472.00, 'quantile_95_out' => 12342116.40],
        'rmsgn1|Gi0/0/1|rhsgn1|Gi0/0/1|pasgn1|ethernet1/1' => ['total_sum_in' => 5683163881647.89, 'total_sum_out' => 5604444368226.41, 'quantile_95_in' => 8539527.43, 'quantile_95_out' => 7554451.91],
        'rmdxb1|Gi0/0/1|rhdxb1|Gi0/0/1|padxb1|ethernet1/1' => ['total_sum_in' => 14395729680816.10, 'total_sum_out' => 9578052913345.43, 'quantile_95_in' => 22233314.27, 'quantile_95_out' => 15314365.06],
        'rmbom1|Gi0/0/1|rhbom1|Gi0/0/1|pabom1|ethernet1/1' => ['total_sum_in' => 10177834150773.60, 'total_sum_out' => 10228136005031.50, 'quantile_95_in' => 15744722.69, 'quantile_95_out' => 13925353.48],
        'rmacc1|Gi0/0/1|rhacc1|Gi0/0/1|paacc1|ethernet1/1' => ['total_sum_in' => 16644764071553.80, 'total_sum_out' => 10685291471298.30, 'quantile_95_in' => 31102579.70, 'quantile_95_out' => 17410573.80],
        'rmcai1|Gi0/0/1|rhcai1|Gi0/0/1|pacai1|ethernet1/1' => ['total_sum_in' => 23939147773900.00, 'total_sum_out' => 18801497266985.70, 'quantile_95_in' => 38488653.97, 'quantile_95_out' => 30696020.03],
        'rmmex1|Gi0/0/1|rhmex1|Gi0/0/1|pamex1|ethernet1/1' => ['total_sum_in' => 30046320641159.50, 'total_sum_out' => 16642331168854.60, 'quantile_95_in' => 55674906.41, 'quantile_95_out' => 32137443.57],
        'rmkhi1|Gi0/0/2|rhkhi1|Gi0/0/2|pakhi1|ethernet1/8' => ['total_sum_in' => 11181132717361.80, 'total_sum_out' => 3465353817315.35, 'quantile_95_in' => 17125605.44, 'quantile_95_out' => 5854326.40],
        'rmmpm1|Gi0/0/2|rhmpm1|Gi0/0/2|pampm1|ethernet1/8' => ['total_sum_in' => 5145495709118.44, 'total_sum_out' => 1460322033675.48, 'quantile_95_in' => 8425048.68, 'quantile_95_out' => 2456725.93],
        'crmpm0|Gi0/0/1|csmpm0|Gi0/0/1|pampm0|ethernet1/1' => ['total_sum_in' => 93943824543.62, 'total_sum_out' => 107562846841.95, 'quantile_95_in' => 98870.91, 'quantile_95_out' => 115178.63],
        'cralg0|Gi0/0/2|csalg0|Gi0/0/2|paalg0|ethernet1/8' => ['total_sum_in' => 2886767223087.15, 'total_sum_out' => 648694441471.37, 'quantile_95_in' => 3979985.73, 'quantile_95_out' => 775523.02],
        'crtgd0|Gi0/0/2|cstgd0|Gi0/0/2|patgd0|ethernet1/8' => ['total_sum_in' => 858088116291.45, 'total_sum_out' => 240928528138.38, 'quantile_95_in' => 1102291.40, 'quantile_95_out' => 234221.21],
        //'crauh0|Gi0/0/2|csauh0|Gi0/0/2|paauh0|ethernet1/8' => ['total_sum_in' => 1229673309804.10, 'total_sum_out' => 309507985920.05, 'quantile_95_in' => 2246475.17, 'quantile_95_out'=> 368490.82],
        'crbgw0|Gi0/0/2|csbgw0|Gi0/0/2' => ['total_sum_in' => 149112576943.68, 'total_sum_out' => 50978687117.93, 'quantile_95_in' => 144381.31, 'quantile_95_out' => 11625.98],
        'crdod0|Gi0/0/2|csdod0|Gi0/0/2|padod0|ethernet1/8' => ['total_sum_in' => 2356679407642.23, 'total_sum_out' => 398734829981.64, 'quantile_95_in' => 4181022.50, 'quantile_95_out' => 468237.92],
        // 'rmlos1|Gi0/0/2|rhlos1|Gi0/0/2|palos1|ethernet1/8' => ['total_sum_in' => 14476571644161.20, 'total_sum_out' => 4887917785655.77, 'quantile_95_in' => 20682174.49, 'quantile_95_out'=> 7152236.82],
    ];


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

    public function buildOrFilter($field, array $values): string
    {
        return implode(' or ', array_map(fn($val) => 'r["' . $field . '"] == "' . $val . '"', $values));
    }

    public function getSumMetrics_origin(Request $request)
    {
        $pairs = [];

        // Collect hostname + interfaces pairs
        for ($i = 1; $i <= 4; $i++) {
            $hostname = $request->input("hostname{$i}");
            $interfaceA = $request->input("interface{$i}_isp_a");
            $interfaceB = $request->input("interface{$i}_isp_b");

            if ($hostname && ($interfaceA || $interfaceB)) {
                $interfaces = array_filter([$interfaceA, $interfaceB]);
                $pairs[] = [
                    'hostname' => addslashes($hostname),
                    'interfaces' => array_map('addslashes', $interfaces),
                ];
            }
        }

        if (empty($pairs)) {
            return response()->json(['error' => 'No valid hostname/interface pairs provided'], 400);
        }

        $start = new \DateTime('first day of last month 00:00:00', new \DateTimeZone('UTC'));
        $stop = new \DateTime('last day of last month 23:59:59', new \DateTimeZone('UTC'));

        $startFormatted = $start->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stop->format('Y-m-d\TH:i:s\Z');

        // Build Flux queries per pair
        $queries = [];
        foreach ($pairs as $pair) {
            $hostname = $pair['hostname'];
            $ifaceFilters = implode(' or ', array_map(fn($iface) => 'r["ifName"] == "' . $iface . '"', $pair['interfaces']));

            $queries[] = <<<FLUX
                    from(bucket: "{$this->bucket}")
                      |> range(start: time(v: "{$startFormatted}"), stop: time(v: "{$stopFormatted}"))
                      |> filter(fn: (r) => r["_measurement"] == "pysnmp")
                      |> filter(fn: (r) => r["device_name"] == "{$hostname}")
                      |> filter(fn: (r) => {$ifaceFilters})
                    FLUX;
        }

        // Union all pairs into one stream
        $unionQuery = 'union(tables: [' . implode(",\n", $queries) . '])';

        // Full Flux query with sum logic
        $flux = <<<FLUX
                    import "sampledata"
                    import "strings"
                    
                    common = {$unionQuery}
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
                      |> fill(usePrevious: true)
                    
                    sum_in = common
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_in", _value: r._value }))
                    
                    sum_out = common
                      |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_out", _value: r._value }))
                      
                    quantile_in = common
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                      |> derivative(unit: 1s, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 39999999)
                       |> aggregateWindow(every: 2h, fn: mean, createEmpty: false)
                      |> group(columns: ["_field"])  // or group by ["device_name", "ifName"] if needed
                      |> quantile(q: 0.95, method: "exact_selector")
                      |> map(fn: (r) => ({ _time: now(), _field: "quantile_95_in", _value: r._value }))
                      
                    quantile_out = common
                      |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                      |> derivative(unit: 1s, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 39999999)
                      |> aggregateWindow(every: 2h, fn: mean, createEmpty: false)
                      |> group(columns: ["_field"])
                      |> quantile(q: 0.95, method: "exact_selector")
                      |> map(fn: (r) => ({ _time: now(), _field: "quantile_95_out", _value: r._value }))

                 union(tables: [sum_in, sum_out, quantile_in, quantile_out])
                FLUX;

        $queryApi = $this->client->createQueryApi();
        $tables = $queryApi->query($flux);

        $results = [];
        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $results[$record->getField()] = $record->getValue();
            }
        }

        return response()->json($results);
    }

    protected function checkAndOverridePredefinedValues(array $pairs): ?array
    {
        if (count($pairs) !== 3) {
            return null;
        }

        // Flatten to array and skip if more than one interface per device
        $flattened = [];
        foreach ($pairs as $pair) {
            if (count($pair['interfaces']) !== 1) {
                return null;
            }
            $flattened[] = $pair['hostname'];
            $flattened[] = $pair['interfaces'][0];
        }

        $key = implode('|', $flattened);

        return $this->predefinedMetrics[$key] ?? null;
    }

    protected function checkAndOverridePredefinedValuesMay(array $pairs): ?array
    {
        if (count($pairs) !== 3) {
            return null;
        }

        // Flatten to array and skip if more than one interface per device
        $flattened = [];
        foreach ($pairs as $pair) {
            if (count($pair['interfaces']) !== 1) {
                return null;
            }
            $flattened[] = $pair['hostname'];
            $flattened[] = $pair['interfaces'][0];
        }

        $key = implode('|', $flattened);
        return $this->predefinedMetricsMay[$key] ?? null;
    }


    public function getSumMetrics(Request $request)
    {
        $pairs = [];

        for ($i = 1; $i <= 4; $i++) {
            $hostname = $request->input("hostname{$i}");
            $interfaceA = $request->input("interface{$i}_isp_a");
            $interfaceB = $request->input("interface{$i}_isp_b");

            if ($hostname && ($interfaceA || $interfaceB)) {
                $interfaces = array_filter([$interfaceA, $interfaceB]);
                $pairs[] = [
                    'hostname' => $hostname,
                    'interfaces' => $interfaces,
                ];
            }
        }

        // Get matching pools
        $pools = Pool::where(function ($query) use ($pairs) {
            foreach ($pairs as $pair) {
                $query->orWhere(function ($q) use ($pair) {
                    $q->whereHas('device', fn($q) => $q->where('hostname', $pair['hostname']))
                        ->whereHas('interface', fn($q) => $q->whereIn('name', $pair['interfaces']));
                });
            }
        })->first();
        // Determine previous month range
        $startPrevMonthStr = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();

        // Initialize totals
        $totalSumIn = 0;
        $totalSumOut = 0;
        $quantile95In = 0;
        $quantile95Out = 0;
        $count = 0;

        $wanStat = WanStatTotal::where('link_name', $pools->name)
                ->where('start_datetime', $startPrevMonthStr)
                ->first();

        if ($wanStat) {
                $totalSumIn += $wanStat->traffic_in;
                $totalSumOut += $wanStat->traffic_out;
                $quantile95In += $wanStat->q_95_in;
                $quantile95Out += $wanStat->q_95_out;
                $count++;
            }


        // Compute average 95th percentile if multiple pools
        if ($count > 0) {
            $quantile95In /= $count;
            $quantile95Out /= $count;
        }

        return response()->json([
            'quantile_95_in' => $quantile95In,
            'quantile_95_out' => $quantile95Out,
            'total_sum_in' => $totalSumIn,
            'total_sum_out' => $totalSumOut,
        ]);
    }
    public function getSumMetrics_norm(Request $request)
    {
        $pairs = [];

        // Collect hostname + interfaces pairs
        for ($i = 1; $i <= 4; $i++) {
            $hostname = $request->input("hostname{$i}");
            $interfaceA = $request->input("interface{$i}_isp_a");
            $interfaceB = $request->input("interface{$i}_isp_b");

            if ($hostname && ($interfaceA || $interfaceB)) {
                $interfaces = array_filter([$interfaceA, $interfaceB]);
                $pairs[] = [
                    'hostname' => addslashes($hostname),
                    'interfaces' => array_map('addslashes', $interfaces),
                ];
            }
        }

        if (count($pairs) == 1) {
            return $this->getSumMetricsOneDeviceInterface($pairs[0]);

        } else {

            if (empty($pairs)) {
                return response()->json(['error' => 'No valid hostname/interface pairs provided'], 400);
            }

            $timezone = new \DateTimeZone('America/New_York');
            $start = new \DateTime('first day of last month 00:00:00', $timezone);
            $stop = new \DateTime('last day of last month 23:59:59', $timezone);
            $stop->modify('last day of this month')->setTime(23, 59, 59);

            $start->setTimezone(new \DateTimeZone('UTC'));
            $stop->setTimezone(new \DateTimeZone('UTC'));

            $startFormatted = $start->format('Y-m-d\TH:i:s\Z');
            $stopFormatted = $stop->format('Y-m-d\TH:i:s\Z');

            // Build Flux queries per pair
            $queries = [];
            foreach ($pairs as $pair) {
                $hostname = $pair['hostname'];
                $ifaceFilters = implode(' or ', array_map(fn($iface) => 'r["ifName"] == "' . $iface . '"', $pair['interfaces']));

                $queries[] = <<<FLUX
                    from(bucket: "{$this->bucket}")
                      |> range(start: time(v: "{$startFormatted}"), stop: time(v: "{$stopFormatted}"))
                      |> filter(fn: (r) => r["_measurement"] == "pysnmp")
                      |> filter(fn: (r) => r["device_name"] == "{$hostname}")
                      |> filter(fn: (r) => {$ifaceFilters})
                    FLUX;
            }

            // Union all pairs into one stream
            $unionQuery = 'union(tables: [' . implode(",\n", $queries) . '])';

            // Full Flux query with sum logic
            $flux = <<<FLUX
                     import "sampledata"
                    import "strings"
                    
                    common = {$unionQuery}
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
                      |> fill(usePrevious: true)
                    
                    sum_in = common
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_in", _value: r._value }))
                    
                    sum_out = common
                      |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_out", _value: r._value }))
                      
                    quantile_in = common
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                      |> derivative(unit: 1s, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 39999999)
                       |> aggregateWindow(every: 2h, fn: mean, createEmpty: false)
                      |> group(columns: ["_field"])  // or group by ["device_name", "ifName"] if needed
                      |> quantile(q: 0.95, method: "exact_selector")
                      |> map(fn: (r) => ({ _time: now(), _field: "quantile_95_in", _value: r._value }))
                      
                    quantile_out = common
                      |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                      |> derivative(unit: 1s, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 39999999)
                      |> aggregateWindow(every: 2h, fn: mean, createEmpty: false)
                      |> group(columns: ["_field"])
                      |> quantile(q: 0.95, method: "exact_selector")
                      |> map(fn: (r) => ({ _time: now(), _field: "quantile_95_out", _value: r._value }))

                 union(tables: [sum_in, sum_out, quantile_in, quantile_out])
                FLUX;


            $queryApi = $this->client->createQueryApi();
            $tables = $queryApi->query($flux);

            $redsults = [];
            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $results[$record->getField()] = $record->getValue();
                }
            }

            // 🔁 Override logic added here
            $override = $this->checkAndOverridePredefinedValues($pairs);
            if ($override !== null) {
                return response()->json($override);
            }

            return response()->json($results);

        }
    }

    public function getSumMetricsOneDeviceInterface($pairs)
    {

        $hostname = $pairs['hostname'];
        $interface = $pairs['interfaces'][0];

        $timezone = new \DateTimeZone('America/New_York');
        $start = new \DateTime('first day of last month 00:00:00', $timezone);
        $stop = new \DateTime('last day of last month 23:59:59', $timezone);
        $stop->modify('last day of this month')->setTime(23, 59, 59);

        $start->setTimezone(new \DateTimeZone('UTC'));
        $stop->setTimezone(new \DateTimeZone('UTC'));

        $startFormatted = $start->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stop->format('Y-m-d\TH:i:s\Z');

        $flux = <<<FLUX
                     import "sampledata"
                     import "strings"
                        common = 
                            from(bucket: "{$this->bucket}")
                            |> range(start: time(v: "{$startFormatted}"), stop: time(v: "{$stopFormatted}"))
                            |> filter(fn: (r) => r["_measurement"] == "pysnmp")
                    
                            |> filter(fn: (r) => r["device_name"] == "{$hostname}")
                            |> filter(fn: (r) => r["ifName"] == "{$interface}")
                            |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                            |> fill(usePrevious: true)
                            
            
                                sum_in = common
                                  |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                                  |> derivative(unit: 1m, nonNegative: true)
                                  |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                                  |> filter(fn: (r) => r._value < 19999999999)
                                  |> group(columns: ["_field"], mode: "by")
                                  // |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                                  |> sum()
                                  |> map(fn: (r) => ({ _time: now(), _field: "total_sum_in", _value: r._value }))
                                
                                sum_out = common
                                  |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                                  |> derivative(unit: 1m, nonNegative: true)
                                  |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                                  |> filter(fn: (r) => r._value < 19999999999)
                                  |> group(columns: ["_field"], mode: "by")
                                  //|> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                                  |> sum()
                                  |> map(fn: (r) => ({ _time: now(), _field: "total_sum_out", _value: r._value }))
            
                                  
                                quantile_in = common
                                    |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                                      |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                                      |> derivative(unit: 1s, nonNegative: true)
                                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 })) 
                                    |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                                      |> group(columns: ["_time", "_field"])
                                      |> sum(column: "_value")
                                      |> group(columns: ["_field"])
                                      |> quantile(q: 0.95, method: "exact_selector")
                                        |> map(fn: (r) => ({ r with _field: "quantile_95_in" })) 
                                      |> yield(name: "q95in")
            
                                  
                                quantile_out = common
                                 |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                                      |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                                      |> derivative(unit: 1s, nonNegative: true)
                                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 })) 
                                    |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                                      |> group(columns: ["_time", "_field"])
                                      |> sum(column: "_value")
                                      |> group(columns: ["_field"])
                                      |> quantile(q: 0.95, method: "exact_selector") 
                                        |> map(fn: (r) => ({ r with _field: "quantile_95_out" }))
                                      |> yield(name: "q95out")
            
                             union(tables: [sum_in, sum_out, quantile_in, quantile_out])
    FLUX;

        $queryApi = $this->client->createQueryApi();
        $tables = $queryApi->query($flux);

        $results = [];
        foreach ($tables as $table) {

            foreach ($table->records as $record) {
                $results[$record->getField()] = $record->getValue();
            }
        }

        // 🔁 Override logic added here
//        dump($results); // just dumps, continues execution
        return response()->json($results);
//        return json_encode($results);
#        if has predefined vals retutn new values if not return $results
    }

    public function getSumMetricsMay(Request $request)
    {
        $pairs = [];

        // Collect hostname + interfaces pairs
        for ($i = 1; $i <= 4; $i++) {
            $hostname = $request->input("hostname{$i}");
            $interfaceA = $request->input("interface{$i}_isp_a");
            $interfaceB = $request->input("interface{$i}_isp_b");

            if ($hostname && ($interfaceA || $interfaceB)) {
                $interfaces = array_filter([$interfaceA, $interfaceB]);
                $pairs[] = [
                    'hostname' => addslashes($hostname),
                    'interfaces' => array_map('addslashes', $interfaces),
                ];
            }
        }

        if (empty($pairs)) {
            return response()->json(['error' => 'No valid hostname/interface pairs provided'], 400);
        }

        $year = 2025;
        $month = 5; // July
        $timezone = new \DateTimeZone('America/New_York');
        $start = new \DateTime("$year-$month-01 00:00:00", $timezone);
        $stop = new \DateTime("$year-$month-01 00:00:00", $timezone);
        $stop->modify('last day of this month')->setTime(23, 59, 59);
        $start->setTimezone(new \DateTimeZone('UTC'));
        $stop->setTimezone(new \DateTimeZone('UTC'));

        $startFormatted = $start->format('Y-m-d\TH:i:s\Z');
        $stopFormatted = $stop->format('Y-m-d\TH:i:s\Z');
        // Build Flux queries per pair
        $queries = [];
        foreach ($pairs as $pair) {
            $hostname = $pair['hostname'];
            $ifaceFilters = implode(' or ', array_map(fn($iface) => 'r["ifName"] == "' . $iface . '"', $pair['interfaces']));

            $queries[] = <<<FLUX
                    from(bucket: "{$this->bucket}")
                      |> range(start: time(v: "{$startFormatted}"), stop: time(v: "{$stopFormatted}"))
                      |> filter(fn: (r) => r["_measurement"] == "pysnmp")
                      |> filter(fn: (r) => r["device_name"] == "{$hostname}")
                      |> filter(fn: (r) => {$ifaceFilters})
                    FLUX;
        }

        // Union all pairs into one stream
        $unionQuery = 'union(tables: [' . implode(",\n", $queries) . '])';

        // Full Flux query with sum logic
        $flux = <<<FLUX
                     import "sampledata"
                    import "strings"
                    
                    common = {$unionQuery}
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets" or r["_field"] == "ifHCOutOctets")
                      |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                      |> fill(usePrevious: true)                 

                    sum_in = common
                      |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      // |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_in", _value: r._value }))
                    
                    sum_out = common
                      |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                      |> derivative(unit: 1m, nonNegative: true)
                      |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 }))
                      |> filter(fn: (r) => r._value < 19999999999)
                      |> group(columns: ["_field"], mode: "by")
                      //|> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                      |> sum()
                      |> map(fn: (r) => ({ _time: now(), _field: "total_sum_out", _value: r._value }))

                      
                    quantile_in = common
                        |> filter(fn: (r) => r["_field"] == "ifHCInOctets")
                          |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                          |> derivative(unit: 1s, nonNegative: true)
                          |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 })) 
                        |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                          |> group(columns: ["_time", "_field"])
                          |> sum(column: "_value")
                          |> group(columns: ["_field"])
                          |> quantile(q: 0.95, method: "exact_selector")
                            |> map(fn: (r) => ({ r with _field: "quantile_95_in" })) 
                          |> yield(name: "q95in")

                      
                    quantile_out = common
                     |> filter(fn: (r) => r["_field"] == "ifHCOutOctets")
                          |> filter(fn: (r) => r._value != 0)  // <== Ignore 0s
                          |> derivative(unit: 1s, nonNegative: true)
                          |> map(fn: (r) => ({ r with _value: float(v: r._value) * 8.0 })) 
                        |> aggregateWindow(every: 1h, fn: mean, createEmpty: false)
                          |> group(columns: ["_time", "_field"])
                          |> sum(column: "_value")
                          |> group(columns: ["_field"])
                          |> quantile(q: 0.95, method: "exact_selector") 
                            |> map(fn: (r) => ({ r with _field: "quantile_95_out" }))
                          |> yield(name: "q95out")

                 union(tables: [sum_in, sum_out, quantile_in, quantile_out])
                FLUX;


        $queryApi = $this->client->createQueryApi();
        $tables = $queryApi->query($flux);

        $results = [];
        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $results[$record->getField()] = $record->getValue();
            }
        }

        // 🔁 Override logic added here
        $override = $this->checkAndOverridePredefinedValuesMay($pairs);
//        dd($override);
        if ($override !== null) {
            return response()->json($override);

        }

        return response()->json($results);
#        if has predefined vals retutn new values if not return $results
    }

}
