<?php

namespace App\Http\Controllers;

use App\Models\InactivePort;

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
            'timeout' => 120, // Increase timeout to 30 seconds
        ]);
    }

    public function getInactivePorts()
    {
        $results = [];

        $devices = [
            "bccfortr",
            "cdgcore1",
            "cdgcore2",
            "cdgcore-spare",
            "cdgobvsw1",
            "cdgvoicesw1",
            "cdgvoicesw2",
            "crabj0",
            "crabv0",
            "cradd0",
            "crala0",
            "cralg0",
            "cramm0",
            "crank0",
            "crapw0",
            "crasb0",
            "crasu0",
            "crauh0",
            "crbak0",
            "crber0",
            "crbey0",
            "crbgf0",
            "crbgw0",
            "crbjl0",
            "crbjm0",
            "crbjs0",
            "crbkk0",
            "crbko0",
            "crbog0",
            "crbru0",
            "crbsb0",
            "crbue0",
            "crbuh0",
            "crbxo0",
            "crbzv0",
            "crcdg0",
            "crcdg1",
            "crcdgx",
            "crcky0",
            "crcmb0",
            "crcoo0",
            "crdac0",
            "crdar0",
            "crdel0",
            "crdel0-55le",
            "crdel0-ht",
            "crdil0",
            "crdkr0",
            "crdod0",
            "crdyu0",
            "crebb0",
            "crevn0",
            "crfih0",
            "crfna0",
            "crfru0",
            "crfun0",
            "crgbe0",
            "crgeo0",
            "crgua0",
            "crgva0",
            "crhah0",
            "crhan0",
            "crhir0",
            "crhnd0",
            "crhre0",
            "cricn0",
            "crisb0",
            "crjib0",
            "crjkt0",
            "crjub0",
            "crkbl0",
            "crkbp0",
            "crkgl0",
            "crkin0",
            "crkiv0",
            "crkrt0",
            "crktm0",
            "crkul0",
            "crkwi0",
            "crlad0",
            "crlbv0",
            "crles0",
            "crlfw0",
            "crlim0",
            "crllw0",
            "crlpb0",
            "crlun0",
            "crmaa0",
            "crmaa1",
            "crmaa1-rmz",
            "crmaa1-sp",
            "crmaa2-rmz",
            "crmaa2-sp",
            "crmaax",
            "crmaax-rmz",
            "crmex0",
            "crmga0",
            "crmnl0",
            "crmpm0",
            "crmru0",
            "crmsq0",
            "crmvd0",
            "crnbo0",
            "crndj0",
            "crnim0",
            "crnkc0",
            "crnyc0",
            "crnyt0",
            "croua0",
            "crpap0",
            "crpbh0",
            "crpit0",
            "crpnh0",
            "crpni0",
            "crpom0",
            "crprn0",
            "crpry0",
            "crpty0",
            "crrai0",
            "crrgn0",
            "crrob0",
            "crrom0",
            "crruh0",
            "crsal0",
            "crscl0",
            "crsho0",
            "crsin0",
            "crsjj0",
            "crsjo0",
            "crskp0",
            "crsof0",
            "crsuv0",
            "crsyd0",
            "crtas0",
            "crtbs0",
            "crtbu0",
            "crtgd0",
            "crtgu0",
            "crtia0",
            "crtms0",
            "crtnr0",
            "crtrw0",
            "crtse0",
            "crtun0",
            "cruln0",
            "crvie0",
            "crvli0",
            "crvte0",
            "crwaw0",
            "crwbg0",
            "crwbg0-gaza",
            "cryao0",
            "crzag0",
            "csabj0",
            "csabv0",
            "csadd0",
            "csala0",
            "csalg0",
            "csamm0",
            "csank0",
            "csapw0",
            "csasb0",
            "csasu0",
            "csauh0",
            "csbak0",
            "csber0",
            "csbey0",
            "csbgf0",
            "csbgw0",
            "csbjl0",
            "csbjm0",
            "csbjs0",
            "csbkk0",
            "csbko0",
            "csbog0",
            "csbru0",
            "csbsb0",
            "csbue0",
            "csbuh0",
            "csbxo0",
            "csbzv0",
            "cscky0",
            "cscmb0",
            "cscoo0",
            "csdac0",
            "csdar0",
            "csdel0",
            "csdel0-55le",
            "csdel0-ht",
            "csdil0",
            "csdkr0",
            "csdod0",
            "csdyu0",
            "csebb0",
            "csevn0",
            "csfih0",
            "csfna0",
            "csfru0",
            "csfun0",
            "csgbe0",
            "csgeo0",
            "csgua0",
            "csgva0",
            "cshah0",
            "cshan0",
            "cshir0",
            "cshnd0",
            "cshre0",
            "csicn0",
            "csisb0",
            "csjib0",
            "csjkt0",
            "csjub0",
            "cskbl0",
            "cskbp0",
            "cskgl0",
            "cskin0",
            "cskiv0",
            "cskrt0",
            "csktm0",
            "cskul0",
            "cskwi0",
            "cslad0",
            "cslbv0",
            "csles0",
            "cslfw0",
            "cslim0",
            "csllw0",
            "cslpb0",
            "cslun0",
            "csmex0",
            "csmga0",
            "csmnl0",
            "csmpm0",
            "csmru0",
            "csmsq0",
            "csmvd0",
            "csnbo0",
            "csndj0",
            "csnim0",
            "csnkc0",
            "csnyc0",
            "csnyt0",
            "csoua0",
            "cspap0",
            "cspbh0",
            "cspit0",
            "cspnh0",
            "cspni0",
            "cspom0",
            "csprn0",
            "cspry0",
            "cspty0",
            "csrai0",
            "csrgn0",
            "csrob0",
            "csrom0",
            "csruh0",
            "cssal0",
            "csscl0",
            "cssho0",
            "cssin0",
            "cssjj0",
            "cssjo0",
            "csskp0",
            "cssof0",
            "cssuv0",
            "cssyd0",
            "cstas0",
            "cstbs0",
            "cstbu0",
            "cstgd0",
            "cstgu0",
            "cstia0",
            "cstms0",
            "cstnr0",
            "cstrw0",
            "cstse0",
            "cstun0",
            "csuln0",
            "csvie0",
            "csvli0",
            "csvte0",
            "cswaw0",
            "cswbg0",
            "cswbg0-gaza",
            "csyao0",
            "cszag0",
            "fdcfortr",
            "fdcwanswitch1",
            "fdcwanswitch2",
            "hancore1",
            "hancore2",
            "jktcore1",
            "jktcore2",
            "maacore1",
            "maacore1-sp",
            "maacore2",
            "maacore2-sp",
            "maacore-spare",
            "maaobvsw1",
            "maaobvsw1-sp",
            "maavoicesw1",
            "maavoicesw1-sp",
            "maavoicesw2",
            "maavoicesw2-sp",
            "nbocore1",
            "nbocore2",
            "polaris1",
            "polaris2",
            "polaris3",
            "rdcgre1",
            "rdcwanswitch1",
            "rdcwanswitch2",
            "rhacc1",
            "rhbeg1",
            "rhbom1",
            "rhcai1",
            "rhcmc1-lab",
            "rhdac1",
            "rhdel1",
            "rhdkr1",
            "rhdla1",
            "rhdxb1",
            "rhfra1",
            "rhhkg1",
            "rhist1",
            "rhjnb1",
            "rhkhi1",
            "rhlon1",
            "rhlos1",
            "rhmex1",
            "rhmow1",
            "rhmpm1",
            "rhsao1",
            "rhsdq1",
            "rhsgn1",
            "rhsita1-lab",
            "rmacc1",
            "rmbeg1",
            "rmbom1",
            "rmcai1",
            "rmcmc1-lab",
            "rmdac1",
            "rmdel1",
            "rmdkr1",
            "rmdla1",
            "rmdxb1",
            "rmfra1",
            "rmhkg1",
            "rmist1",
            "rmjnb1",
            "rmkhi1",
            "rmlon1",
            "rmlos1",
            "rmmex1",
            "rmmow1",
            "rmmpm1",
            "rmsao1",
            "rmsdq1",
            "rmsgn1",
            "rmsita1-lab",
            "sdcwanswitch1",
            "sdcwanswitch2",
            "wdcgre1",
            "xvcdg01",
            "xvcdg02",
            "xvmaa01",
            "xvmaa02",
            "xvrtr01",
            "xvrtr02",
        ];
        $devices = ["maaed10sw1",
            "swabj0",
            "swabj1",
            "swabj2",
            "swabj3",
            "swabj4",
            "swabj5",
            "swabjx",
            "swabv0",
            "swabv1",
            "swabv2",
            "swabv3",
            "swabv4",
            "swabv5",
            "swabv6",
            "swabv7",
            "swabvx",
            "swadd0",
            "swadd1",
            "swadd2",
            "swadd3",
            "swadd4",
            "swadd5",
            "swadd6",
            "swadd7",
            "swaddx",
            "swala0",
            "swala1",
            "swala2",
            "swala3",
            "swala4",
            "swalax",
            "swalg0",
            "swalg1",
            "swalgx",
            "swamm0",
            "swamm1",
            "swamm2",
            "swamm3",
            "swamm4",
            "swammx",
            "swank0",
            "swank1",
            "swank2",
            "swank3",
            "swankx",
            "swapw0",
            "swapw1",
            "swasb0",
            "swasb1",
            "swasbx",
            "swasu0",
            "swasu1",
            "swasux",
            "swauh0",
            "swauh1",
            "swauhx",
            "swbak0",
            "swbak1",
            "swbak2",
            "swbakx",
            "swber0",
            "swber1",
            "swbey0",
            "swbey1",
            "swbey2",
            "swbey3",
            "swbey4",
            "swbey5",
            "swbeyx",
            "swbgf0",
            "swbgf1",
            "swbgf2",
            "swbgfx",
            "swbgw0",
            "swbgw1",
            "swbgw2",
            "swbgwx",
            "swbjl0",
            "swbjl1",
            "swbjlx",
            "swbjm0",
            "swbjm1",
            "swbjm2",
            "swbjmx",
            "swbjs0",
            "swbjs1",
            "swbjs2",
            "swbjs3",
            "swbjs4",
            "swbjs5",
            "swbjs6",
            "swbjs7",
            "swbjs8",
            "swbjsx",
            "swbkk0",
            "swbkk1",
            "swbkk2",
            "swbkk3",
            "swbkk4",
            "swbkk5",
            "swbkk6",
            "swbkk7",
            "swbkk8",
            "swbkkx",
            "swbko0",
            "swbko1",
            "swbko2",
            "swbko3",
            "swbkox",
            "swbog0",
            "swbog1",
            "swbog2",
            "swbog3",
            "swbog4",
            "swbog5",
            "swbog6",
            "swbog7",
            "swbogx",
            "swbru0",
            "swbru1",
            "swbru2",
            "swbru3",
            "swbru4",
            "swbrux",
            "swbsb0",
            "swbsb1",
            "swbsb2",
            "swbsb3",
            "swbsb4",
            "swbsb5",
            "swbsbx",
            "swbue0",
            "swbue1",
            "swbue2",
            "swbue3",
            "swbue4",
            "swbue5",
            "swbues",
            "swbuex",
            "swbuh0",
            "swbuh1",
            "swbuh2",
            "swbuh3",
            "swbuh4",
            "swbuh5",
            "swbuhx",
            "swbxo0",
            "swbxo1",
            "swbxox",
            "swbzv0",
            "swbzv1",
            "swbzv2",
            "swbzvx",
            "swcdg0",
            "swcdg1",
            "swcdg10",
            "swcdg11",
            "swcdg12",
            "swcdg13",
            "swcdg14",
            "swcdg15",
            "swcdg2",
            "swcdg3",
            "swcdg4",
            "swcdg5",
            "swcdg6",
            "swcdg7",
            "swcdg8",
            "swcdg9",
            "swcdgx",
            "swcky0",
            "swcky1",
            "swcky2",
            "swckyx",
            "swcmb0",
            "swcmb1",
            "swcmb2",
            "swcmb3",
            "swcmb4",
            "swcmb5",
            "swcmb6",
            "swcmb7",
            "swcmb8",
            "swcmb9",
            "swcmbx",
            "swcmc0-lab",
            "swcoo0",
            "swcoo1",
            "swcoo2",
            "swcoox",
            "swdac0",
            "swdac1",
            "swdac2",
            "swdac3",
            "swdac4",
            "swdac5",
            "swdac6",
            "swdac7",
            "swdac8",
            "swdacx",
            "swdar0",
            "swdar1",
            "swdar2",
            "swdar3",
            "swdar4",
            "swdar5",
            "swdar6",
            "swdarx",
            "swdel0",
            "swdel0-55le",
            "swdel0-ht",
            "swdel1",
            "swdel1-55le",
            "swdel1-ht",
            "swdel2",
            "swdel2-ht",
            "swdel3",
            "swdel3-ht",
            "swdel4",
            "swdel4-ht",
            "swdel5",
            "swdel5-ht",
            "swdel6",
            "swdel6-ht",
            "swdel7",
            "swdel7-ht",
            "swdel8",
            "swdel8-ht",
            "swdel9-ht",
            "swdelx-55le",
            "swdelx-ht",
            "swdil0",
            "swdil1",
            "swdil2",
            "swdil3",
            "swdilx",
            "swdkr0",
            "swdkr1",
            "swdkr2",
            "swdkr3",
            "swdkr4",
            "swdkr5",
            "swdkrx",
            "swdod0",
            "swdod1",
            "swdodx",
            "swdyu0",
            "swdyu1",
            "swdyu2",
            "swdyu3",
            "swdyu4",
            "swdyux",
            "swebb0",
            "swebb1",
            "swebb2",
            "swebb3",
            "swebb4",
            "swebbx",
            "swevn0",
            "swevn1",
            "swevn2",
            "swevnx",
            "swfih0",
            "swfih1",
            "swfih2",
            "swfih3",
            "swfih4",
            "swfih5",
            "swfihx",
            "swfna0",
            "swfna1",
            "swfna2",
            "swfna3",
            "swfna4",
            "swfnax",
            "swfru0",
            "swfru1",
            "swfru2",
            "swfru3",
            "swfru4",
            "swfru5",
            "swfrux",
            "swfun0",
            "swfunx",
            "swgbe0",
            "swgbe1",
            "swgbex",
            "swgeo0",
            "swgeo1",
            "swgeox",
            "swgua0",
            "swgua1",
            "swgua2",
            "swguax",
            "swgva0",
            "swgva1",
            "swgvax",
            "swhah0",
            "swhah1",
            "swhahx",
            "swhan0",
            "swhan1",
            "swhan10",
            "swhan11",
            "swhan12",
            "swhan13",
            "swhan2",
            "swhan3",
            "swhan4",
            "swhan5",
            "swhan6",
            "swhan7",
            "swhan8",
            "swhan9",
            "swhanx",
            "swhir0",
            "swhir1",
            "swhir2",
            "swhirx",
            "swhnd0",
            "swhnd1",
            "swhnd2",
            "swhnd3",
            "swhnd4",
            "swhndx",
            "swhre0",
            "swhre1",
            "swhre2",
            "swhrex",
            "swicn0",
            "swicn1",
            "swicn2-sel",
            "swicn3-sel",
            "swicnx",
            "swisb0",
            "swisb1",
            "swisb10",
            "swisb2",
            "swisb3",
            "swisb4",
            "swisb5",
            "swisb6",
            "swisb7",
            "swisb8",
            "swisb9",
            "swisbx",
            "swjib0",
            "swjib1",
            "swjibx",
            "swjkt0",
            "swjkt1",
            "swjkt10",
            "swjkt11",
            "swjkt12",
            "swjkt13",
            "swjkt14",
            "swjkt17",
            "swjkt18",
            "swjkt19",
            "swjkt2",
            "swjkt20",
            "swjkt22",
            "swjkt23",
            "swjkt24",
            "swjkt4",
            "swjkt5",
            "swjkt6",
            "swjkt7",
            "swjkt8",
            "swjkt9",
            "swjktx",
            "swjub0",
            "swjub1",
            "swjub2",
            "swjub3",
            "swjub4",
            "swjub5",
            "swjubx",
            "swkbl0",
            "swkbl1",
            "swkbl10",
            "swkbl11",
            "swkbl12",
            "swkbl2",
            "swkbl3",
            "swkbl4",
            "swkbl5",
            "swkbl6",
            "swkbl7",
            "swkbl8",
            "swkbl9",
            "swkblx",
            "swkbp0",
            "swkbp1",
            "swkbp2",
            "swkbpx",
            "swkgl0",
            "swkgl1",
            "swkgl2",
            "swkgl3",
            "swkglx",
            "swkin0",
            "swkin1",
            "swkin2",
            "swkinx",
            "swkiv0",
            "swkiv1",
            "swkiv2",
            "swkivx",
            "swkrt0",
            "swkrt1",
            "swkrt2",
            "swkrtx",
            "swktm0",
            "swktm1",
            "swktm2",
            "swktm3",
            "swktm4",
            "swktm5",
            "swktm6",
            "swktm7",
            "swktmx",
            "swkul0",
            "swkul1",
            "swkul2",
            "swkulx",
            "swkwi0",
            "swkwi1",
            "swkwi2",
            "swkwix",
            "swlad0",
            "swlad1",
            "swlad2",
            "swlad3",
            "swladx",
            "swlbv0",
            "swlbv1",
            "swlbvx",
            "swles0",
            "swles1",
            "swlesx",
            "swlfw0",
            "swlfw1",
            "swlfw2",
            "swlfw3",
            "swlfwx",
            "swlim0",
            "swlim1",
            "swlim2",
            "swlim3",
            "swlim4",
            "swlimx",
            "swllw0",
            "swllw1",
            "swllw2",
            "swllw3",
            "swlpb0",
            "swlpb1",
            "swlpb2",
            "swlpb3",
            "swlpbx",
            "swlun0",
            "swlun1",
            "swlun2",
            "swlun3",
            "swlunx",
            "swmaa0",
            "swmaa0-rmz",
            "swmaa0-sp",
            "swmaa1",
            "swmaa10",
            "swmaa10-rmz",
            "swmaa11",
            "swmaa12",
            "swmaa13",
            "swmaa14",
            "swmaa15",
            "swmaa16",
            "swmaa17",
            "swmaa18",
            "swmaa19",
            "swmaa1-rmz",
            "swmaa1-sp",
            "swmaa2",
            "swmaa20",
            "swmaa21",
            "swmaa22",
            "swmaa23",
            "swmaa24",
            "swmaa25",
            "swmaa26",
            "swmaa27",
            "swmaa28",
            "swmaa29",
            "swmaa2-rmz",
            "swmaa2-sp",
            "swmaa3",
            "swmaa30",
            "swmaa31",
            "swmaa33",
            "swmaa34",
            "swmaa35",
            "swmaa36",
            "swmaa3-rmz",
            "swmaa4",
            "swmaa4-rmz",
            "swmaa5",
            "swmaa5-rmz",
            "swmaa6",
            "swmaa6-rmz",
            "swmaa7",
            "swmaa7-rmz",
            "swmaa8",
            "swmaa8-rmz",
            "swmaa9",
            "swmaa9-rmz",
            "swmaax",
            "swmaax-sp",
            "swmaax-sp-93k",
            "swmex0",
            "swmex1",
            "swmex2",
            "swmex3",
            "swmexx",
            "swmga0",
            "swmga1",
            "swmga2",
            "swmgax",
            "swmnl0",
            "swmnl1",
            "swmnl2",
            "swmnl3",
            "swmnl4",
            "swmnl5",
            "swmnl6",
            "swmnl7",
            "swmnl8",
            "swmnl9",
            "swmnlx",
            "swmpm0",
            "swmpm1",
            "swmpm2",
            "swmpm3",
            "swmpm4",
            "swmpmx",
            "swmru0",
            "swmru1",
            "swmrux",
            "swmsq0",
            "swmsq1",
            "swmsqx",
            "swmvd0",
            "swmvd1",
            "swmvdx",
            "swnbo0",
            "swnbo1",
            "swnbo10",
            "swnbo11",
            "swnbo2",
            "swnbo3",
            "swnbo4",
            "swnbo5",
            "swnbo6",
            "swnbo7",
            "swnbo8",
            "swnbo9",
            "swnbox",
            "swnbox-93k",
            "swndj0",
            "swndj1",
            "swndj2",
            "swndjx",
            "swnim0",
            "swnim1",
            "swnim2",
            "swnim3",
            "swnimx",
            "swnkc0",
            "swnkc1",
            "swnkc2",
            "swnkcx",
            "swnyc0",
            "swnyc1",
            "swnycx",
            "swnyt0",
            "swnyt1",
            "swnytx",
            "swoua0",
            "swoua1",
            "swoua2",
            "swoua3",
            "swouax",
            "swpap0",
            "swpap1",
            "swpap2",
            "swpapx",
            "swpbh0",
            "swpbh1",
            "swpbhx",
            "swpit0",
            "swpit1",
            "swpit2",
            "swpitx",
            "swpnh0",
            "swpnh1",
            "swpnh2",
            "swpnh3",
            "swpnh4",
            "swpnhx",
            "swpni0",
            "swpni1",
            "swpnix",
            "swpom0",
            "swpom1",
            "swpom2",
            "swpomx",
            "swprn0",
            "swprn1",
            "swprn2",
            "swprnx",
            "swpry0",
            "swpry1",
            "swpry2",
            "swpry3",
            "swpry4",
            "swpry5",
            "swpryx",
            "swpty0",
            "swpty1",
            "swpty2",
            "swpty3",
            "swpty4",
            "swpty5",
            "swptyx",
            "swrai0",
            "swrai1",
            "swraix",
            "swrba0",
            "swrba1",
            "swrba2",
            "swrba3",
            "swrba4",
            "swrbax",
            "swrgn0",
            "swrgn1",
            "swrgn2",
            "swrgn3",
            "swrgn4",
            "swrgn5",
            "swrgn6",
            "swrgnx",
            "swrob0",
            "swrob1",
            "swrob2",
            "swrob3",
            "swrobx",
            "swrom0",
            "swrom1",
            "swrom2",
            "swrom3",
            "swromx",
            "swruh0",
            "swruh1",
            "swruh2",
            "swruhx",
            "swsal0",
            "swsal1",
            "swsalx",
            "swscl0",
            "swscl1",
            "swsho0",
            "swshox",
            "swsin0",
            "swsin1",
            "swsin10",
            "swsin2",
            "swsin3",
            "swsin4",
            "swsin5",
            "swsin6",
            "swsin7",
            "swsin8",
            "swsin9",
            "swsinx",
            "swsita0-lab",
            "swsjj0",
            "swsjj1",
            "swsjj2",
            "swsjj3",
            "swsjjx",
            "swsjo0",
            "swsjo1",
            "swskp0",
            "swskp1",
            "swskp2",
            "swskpx",
            "swsof0",
            "swsof1",
            "swsof2",
            "swsof3",
            "swsof4",
            "swsof5",
            "swsof6",
            "swsof7",
            "swsofx",
            "swsuv0",
            "swsuv1",
            "swsuv2",
            "swsuvx",
            "swsyd0",
            "swsyd1",
            "swsyd2",
            "swsyd3",
            "swsyd4",
            "swsyd5",
            "swsyd6",
            "swsyd7",
            "swsydx",
            "swtas0",
            "swtas1",
            "swtas2",
            "swtas3",
            "swtas4",
            "swtas5",
            "swtasx",
            "swtbs0",
            "swtbs1",
            "swtbs2",
            "swtbs3",
            "swtbsx",
            "swtbu0",
            "swtgd0",
            "swtgd1",
            "swtgdx",
            "swtgu0",
            "swtgu1",
            "swtgu2",
            "swtgux",
            "swtia0",
            "swtia1",
            "swtia2",
            "swtia3",
            "swtiax",
            "swtms0",
            "swtms1",
            "swtnr0",
            "swtnr1",
            "swtnr2",
            "swtnr3",
            "swtnrx",
            "swtrw0",
            "swtrwx",
            "swtse0",
            "swtse1",
            "swtse2",
            "swtsex",
            "swtun0",
            "swtun1",
            "swtun2",
            "swtun3",
            "swtunx",
            "swuio0",
            "swuio1",
            "swuio2",
            "swuiox",
            "swuln0",
            "swuln1",
            "swuln2",
            "swuln3",
            "swulnx",
            "swvie0",
            "swvie1",
            "swvie10",
            "swvie11",
            "swvie12",
            "swvie13",
            "swvie14",
            "swvie15",
            "swvie2",
            "swvie3",
            "swvie4",
            "swvie5",
            "swvie6",
            "swvie7",
            "swvie8",
            "swvie9",
            "swviex",
            "swvli0",
            "swvli1",
            "swvlix",
            "swvte0",
            "swwaw0",
            "swwaw1",
            "swwaw2",
            "swwaw3",
            "swwawx",
            "swwbg0",
            "swwbg0-gaza",
            "swwbg1",
            "swwbg1-gaza",
            "swwbg2",
            "swwbg3",
            "swwbgx",
            "swwbgx-gaza",
            "swyao0",
            "swyao1",
            "swyao2",
            "swyao3",
            "swyao4",
            "swyaox",
            "swzag0",
            "swzag1",
            "swzag2",
            "swzag3",
            "swzagx",
        ];


        foreach ($devices as $device) {
            $flux = <<<EOT
from(bucket: "snmp_1")
  |> range(start: -90d)   // use relative time (last 90 days)
  |> filter(fn: (r) => r["_measurement"] == "pysnmp")
  |> filter(fn: (r) => r["device_name"] == "$device")
  |> filter(fn: (r) => r["_field"] == "ifHCOutOctets" or r["_field"] == "ifHCInOctets")
  |> filter(fn: (r) => exists r._value)
  |> group(columns: ["device_name","ifDescr","_field"])
  |> sum(column: "_value")
  |> group(columns: ["device_name","ifDescr"])
  |> pivot(rowKey:["device_name","ifDescr"], columnKey: ["_field"], valueColumn: "_value")
  |> map(fn: (r) => ({
        device_name: r.device_name,
        ifDescr: r.ifDescr,
        total: (if exists r.ifHCInOctets then r.ifHCInOctets else 0) +
               (if exists r.ifHCOutOctets then r.ifHCOutOctets else 0)
  }))
  |> filter(fn: (r) => r.total == 0)
  |> map(fn: (r) => ({ _field:r.ifDescr, device_name:r.device_name }))
EOT;

            $queryApi = $this->client->createQueryApi();
            $queryResult = $queryApi->query($flux);

            foreach ($queryResult as $table) {
                foreach ($table->records as $record) {
//                    $results[] = [
//                        "device" => $record->values["device_name"],
//                        "port"   => $record->values["_field"]
//                    ];
//                    dump($record->values["device_name"], $record->values["_field"]);
                    InactivePort::firstOrCreate([
                        'device_name' => $record->values["device_name"],
                        'port'        => $record->values["_field"]
                    ]);


                    echo $record->values["device_name"] . "," . $record->values["_field"] . "\n";
                }
            }
        }

//        return $results;
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
