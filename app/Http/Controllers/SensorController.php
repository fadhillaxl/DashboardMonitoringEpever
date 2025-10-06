<?php

namespace App\Http\Controllers;

use App\Http\Requests\SensorDataReceiveRequest;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Services\InfluxService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class SensorController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    public function showSite($mac_address)
    {
        $query = "
        SELECT 
            \"pt-100-temperature-1\", \"pt-100-temperature-2\", \"pt-100-temperature-3\",
            \"pt-100-temperature-4\", \"pt-100-temperature-5\", \"pt-100-temperature-6\",
            \"pt-100-temperature-7\", \"pt-100-temperature-8\", \"pt-100-temperature-9\",
            \"pt-100-temperature-10\", \"pt-100-temperature-11\", \"pt-100-temperature-12\",
            \"pt-100-temperature-13\", \"pt-100-temperature-14\",
            \"pt-100-status-1\", \"pt-100-status-2\", \"pt-100-status-3\", \"pt-100-status-4\",
            \"pt-100-status-5\", \"pt-100-status-6\", \"pt-100-status-7\", \"pt-100-status-8\",
            \"pt-100-status-9\", \"pt-100-status-10\", \"pt-100-status-11\", \"pt-100-status-12\",
            \"pt-100-status-13\", \"pt-100-status-14\",
            \"thm-30md-humidity-1\", \"thm-30md-humidity-2\",
            \"thm-30md-humidity-3\", \"thm-30md-humidity-4\",
            \"thm-30md-status-1\", \"thm-30md-status-2\", \"thm-30md-status-3\", \"thm-30md-status-4\",
            time
        FROM sensor_rs485
        WHERE mac_address = '$mac_address'
        ORDER BY time DESC
        LIMIT 1
    ";

        $result = $this->influx->query($query);

        $series  = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'][0] ?? null;

        $temperatures = [];
        $humidities   = [];
        $time         = null;

        if ($values) {
            $assoc = array_combine($columns, $values);

            foreach ($assoc as $key => $val) {
                // Temperature
                if (str_starts_with($key, 'pt-100-temperature-')) {
                    $num = str_replace('pt-100-temperature-', '', $key);
                    $temperatures[] = [
                        'label'  => "Temperature $num",
                        'value'  => $val,
                        'status' => $assoc["pt-100-status-$num"] ?? null,
                    ];
                }
                // Humidity
                elseif (str_starts_with($key, 'thm-30md-humidity-')) {
                    $num = str_replace('thm-30md-humidity-', '', $key);
                    $humidities[] = [
                        'label'  => "Humidity $num",
                        'value'  => $val,
                        'status' => $assoc["thm-30md-status-$num"] ?? null,
                    ];
                }
                // Time
                elseif ($key === 'time' && !empty($val)) {
                    $time = \Carbon\Carbon::parse($val)
                        ->setTimezone('Asia/Jakarta')
                        ->format('d M Y H:i:s');
                }
            }
        }

        $sites = Site::pluck('mac_address')->toArray();

        return view('dashboard.sensors.index', [
            'mac_address'    => $mac_address,
            'temperatures'   => $temperatures,
            'humidities'     => $humidities,
            'time'           => $time,
            'macs_menu_map'  => $sites,
        ]);
    }

    public function showCharts($mac_address, Request $request)
    {
        $range = $request->get('range', '15m');
        $now   = now()->toImmutable();

        // Ambil custom start & end date
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        if ($startDate) {
            $start = \Carbon\Carbon::parse($startDate)->toImmutable();
            $end   = $endDate ? \Carbon\Carbon::parse($endDate)->toImmutable() : $start->copy()->endOfDay();
        } else {
            $ranges = [
                '15m' => fn() => [$now->subMinutes(15), $now],
                '1h'  => fn() => [$now->subHour(), $now],
                '1d'  => fn() => [$now->subDay(), $now],
                '1w'  => fn() => [$now->subWeek(), $now],
                '1m'  => fn() => [$now->subMonth(), $now],
                '1y'  => fn() => [$now->subYear(), $now],
            ];
            [$start, $end] = ($ranges[$range] ?? $ranges['1h'])();
        }

        // Query InfluxDB
        $query = "
        SELECT 
            \"pt-100-temperature-1\", \"pt-100-temperature-2\", \"pt-100-temperature-3\",
            \"pt-100-temperature-4\", \"pt-100-temperature-5\", \"pt-100-temperature-6\",
            \"pt-100-temperature-7\", \"pt-100-temperature-8\", \"pt-100-temperature-9\",
            \"pt-100-temperature-10\", \"pt-100-temperature-11\", \"pt-100-temperature-12\",
            \"pt-100-temperature-13\", \"pt-100-temperature-14\",
            \"thm-30md-humidity-1\", \"thm-30md-humidity-2\",
            \"thm-30md-humidity-3\", \"thm-30md-humidity-4\",
            time
        FROM sensor_rs485
        WHERE mac_address = '$mac_address' 
          AND time >= '{$start->toIso8601String()}' 
          AND time <= '{$end->toIso8601String()}'
        ORDER BY time ASC
    ";

        $result = $this->influx->query($query);
        $series = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'] ?? [];

        // Transformasi data
        $rows = array_map(function ($row) use ($columns) {
            $assoc = array_combine($columns, $row);

            $renamed = [];
            foreach ($assoc as $key => $val) {
                if (str_starts_with($key, 'pt-100-temperature-')) {
                    $num = str_replace('pt-100-temperature-', '', $key);
                    $renamed["Temperature $num"] = $val;
                } elseif (str_starts_with($key, 'thm-30md-humidity-')) {
                    $num = str_replace('thm-30md-humidity-', '', $key);
                    $renamed["Humidity $num"] = $val;
                }
            }

            if (isset($assoc['time'])) {
                $renamed['time'] = \Carbon\Carbon::parse($assoc['time'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y H:i:s');
            }

            return $renamed;
        }, $values);

        $lastRow = end($rows);
        $sites   = Site::pluck('mac_address')->toArray();

        // Label mapping
        $labels = [];
        foreach (range(1, 14) as $i) {
            $labels["Temperature $i"] = "Temperature Sensor $i (°C)";
        }
        foreach (range(1, 4) as $i) {
            $labels["Humidity $i"] = "Humidity Sensor $i (%RH)";
        }

        $availableRanges = ['15m', '1h', '1d', '1w', '1m', '1y'];
        $chartColumns    = array_filter(array_keys($rows[0] ?? []), fn($c) => $c != 'time');
        $macs_menu_map = Site::pluck('mac_address')->toArray();


        return view('dashboard.sensors.charts', compact(
            'mac_address',
            'rows',
            'range',
            'lastRow',
            'macs_menu_map',
            'labels',
            'availableRanges',
            'chartColumns',
            'startDate',
            'endDate'
        ));
    }

    public function exportExcel($mac_address, Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        // Kalau kosong, fallback ke range default (misal 1 jam terakhir)
        if ($startDate) {
            $start = \Carbon\Carbon::parse($startDate)->toImmutable();
            $end   = $endDate ? \Carbon\Carbon::parse($endDate)->toImmutable() : $start->copy()->endOfDay();
        } else {
            $now   = now()->toImmutable();
            $start = $now->subHour(); // default 1 jam
            $end   = $now;
        }

        $query = "
         SELECT 
            \"pt-100-temperature-1\", \"pt-100-temperature-2\", \"pt-100-temperature-3\",
            \"pt-100-temperature-4\", \"pt-100-temperature-5\", \"pt-100-temperature-6\",
            \"pt-100-temperature-7\", \"pt-100-temperature-8\", \"pt-100-temperature-9\",
            \"pt-100-temperature-10\", \"pt-100-temperature-11\", \"pt-100-temperature-12\",
            \"pt-100-temperature-13\", \"pt-100-temperature-14\",
            \"thm-30md-humidity-1\", \"thm-30md-humidity-2\",
            \"thm-30md-humidity-3\", \"thm-30md-humidity-4\",
            time
        FROM sensor_rs485
        WHERE mac_address = '$mac_address' 
          AND time >= '{$start->toIso8601String()}' 
          AND time <= '{$end->toIso8601String()}'
        ORDER BY time ASC
    ";
        $result = $this->influx->query($query);

        $series  = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'] ?? [];

        $rows = array_map(function ($row) use ($columns) {
            $assoc = array_combine($columns, $row);
            if (isset($assoc['time'])) {
                $assoc['time'] = \Carbon\Carbon::parse($assoc['time'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y H:i:s');
            }
            return $assoc;
        }, $values);

        if (count($rows) === 0) {
            return redirect()
                ->back()
                ->with('error', 'Data Export tidak tersedia untuk rentang waktu yang dipilih.');
        }

        $rowsArray = array_map(fn($row) => array_values($row), $rows);

        $filename = "RS485Sensors_{$mac_address}_" . now()->format('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(
            new \App\Exports\SensorsExport($rowsArray, $columns),
            $filename
        );
    }
}
