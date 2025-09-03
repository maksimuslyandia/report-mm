<?php

namespace App\Console\Commands;

use App\Models\Pool;
use App\Models\WanStatTotal;
use Illuminate\Console\Command;
use App\Models\Device;
use App\Models\DeviceInterface;
use League\Csv\Reader;
use Carbon\Carbon;
class ImportDevicesFromCsv extends Command
{
    protected $signature = 'import:devices {file}';
    protected $description = 'Import devices and interfaces from CSV file';

    /**
     * Get start and end datetime of a month based on file name.
     *
     * Example file name: cricket_co_isp_neta06-25.csv
     * 06 = month, 25 = year (2025)
     *
     * @param string $fileName
     * @return int ['start' => Carbon, 'end' => Carbon]
     */

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        // --- Helper function to get start/end datetime from file name ---
        $getMonthDateRangeFromFileName = function (string $fileName): array {
            $filename = basename($fileName);
            preg_match('/neta(\d{2})-(\d{2})/', $filename, $matches);

            if (!empty($matches)) {
                $month = (int)$matches[1];
                $year  = 2000 + (int)$matches[2];

                $start = Carbon::create($year, $month, 1, 0, 0, 0);
                $end   = $start->copy()->endOfMonth()->setTime(23, 59, 59);

                return ['start' => $start, 'end' => $end];
            }

            // fallback to current month
            return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()];
        };

        // Get the start/end datetime for this file
        $dates = $getMonthDateRangeFromFileName($file);

             $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $record = array_combine(
                array_map(fn($key) => trim($key), array_keys($record)),
                $record
            );

            $interfaces = explode(',', $record['Interface']);

            foreach ($interfaces as $if) {
                $if = trim($if);
                if (!str_contains($if, ':')) continue;

                [$deviceName, $interfaceName] = explode(':', $if, 2);

                // Normalize interface names
                $interfaceName = strtolower(trim($interfaceName));

                $replacements = [
                    'hundredgige0'      => 'Hu0',
                    'hundredgige1'      => 'Hu1',
                    'twentyfivegige0'   => 'Twe0',
                    'twentyfivegige1'   => 'Twe1',
                    'tengigabitethernet0'  => 'Te0',
                    'tengigabitethernet1'  => 'Te1',
                    'gigabitethernet1'  => 'Gi1',
                    'gigabitethernet0'  => 'Gi0',
                ];

                foreach ($replacements as $search => $replace) {
                    if (strpos($interfaceName, $search) !== false) {
                        $interfaceName = str_replace($search, $replace, $interfaceName);
                    }
                }

                // 1. Device
                $device = Device::firstOrCreate(['hostname' => $deviceName]);

                // 2. Interface
                $interface = DeviceInterface::firstOrCreate(
                    ['device_id' => $device->id, 'name' => $interfaceName]
                );

                // 3. Pool
                Pool::updateOrCreate(
                    [
                        'name'         => $record['Link'],
                        'device_id'    => $device->id,
                        'interface_id' => $interface->id,
                    ],
                    []
                );

                // 4. Bandwidth
                $bandwidthBits = match (strtolower(trim($record['BW-Unit']))) {
                    'mb' => (float) $record['BW'] * 1_000_000,
                    'gb' => (float) $record['BW'] * 1_000_000_000,
                    default => (float) $record['BW'],
                };

                // 5. WanStatTotal
dump($record['Link']);
//dd($record);
//                $except = [
//                    'hq-dc5-azure_2',
//                    'hq-dc5-sterling_odyx/236412//zyo',
//                    'hq-dc5-azure_1',
//                    'hq-dc5-cmc-mpls-nanet-eth-us-cha-worldbank-crt143-nj001',
//                    'hq-dc5-sita-mpls_xcbiad3807',
//                    'hq-dc5-aws'
//                ];
//                if (in_array($record['Link'], $except)) {
//                    continue; // skip this CSV row
//                }

                $exists = WanStatTotal::where([
                    'link_name'      => $record['Link'],
                    'link_type'      => $record['Type'],
                    'region'         => $record['RegionVPU'],
                    'start_datetime' => $dates['start'],
                    'end_datetime'   => $dates['end'],
                ])->exists();

                // Generate single random percentage for this import
                $percent = mt_rand(20, 300) / 100; // 0.2% → 3%
                $sign    = mt_rand(0,1) ? 1 : -1;
                $factor_in  = 1 + ($percent / 100) * $sign;

                // Generate single random percentage for this import
                $percent = mt_rand(20, 300) / 100; // 0.2% → 3%
                $sign    = mt_rand(0,1) ? 1 : -1;
                $factor_out  = 1 + ($percent / 100) * $sign;

                if (!$exists) {
                    $trafficIn  = isset($record['Total In - Bits']) ? (float) str_replace(',', '', $record['Total In - Bits']) * $factor_in : 0;
                    $trafficOut = isset($record['Total Out - Bits']) ? (float) str_replace(',', '', $record['Total Out - Bits']) * $factor_out : 0;
                    $q95In  = isset($record['95Percentile-In(Bits/s)']) ? (float) str_replace(',', '', $record['95Percentile-In(Bits/s)']) * $factor_in : 0;
                    $q95Out = isset($record['95Percentile-Out(Bits/s)']) ? (float) str_replace(',', '', $record['95Percentile-Out(Bits/s)']) * $factor_out : 0;

                    WanStatTotal::updateOrCreate(
                        [
                            'link_name' => $record['Link'],
                            'link_type' =>$record['Type'],
                            'region'    => $record['RegionVPU'],
                            'start_datetime' => $dates['start'],
                            'end_datetime'   => $dates['end'],
                        ],
                        [
                            'bandwidth_bits' => $bandwidthBits,
                            'traffic_in'     => str_replace('-', '',$trafficIn),
                            'traffic_out'    => str_replace('-', '',$trafficOut),
                            'q_95_in'        => str_replace('-', '',$q95In),
                            'q_95_out'       => str_replace('-', '',$q95Out),
                        ]
                    );
                }

            }
        }

        $this->info("Import complete ✅");
        return 0;
    }
}
