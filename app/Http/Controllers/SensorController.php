<?php

namespace App\Http\Controllers;

use App\Http\Requests\SensorDataReceiveRequest;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Services\InfluxService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

        return view('sensors.index', [
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
        $now = now()->toImmutable();

        // Tentukan start time dari range
        switch ($range) {
            case '15m':
                $start = $now->subMinutes(15);
                break;
            case '1h':
                $start = $now->subHour();
                break;
            case '1d':
                $start = $now->subDay();
                break;
            case '1w':
                $start = $now->subWeek();
                break;
            case '1m':
                $start = $now->subMonth();
                break;
            case '1y':
                $start = $now->subYear();
                break;
            default:
                $start = $now->subHour();
                break;
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
        WHERE mac_address = '$mac_address' AND time >= '{$start->toIso8601String()}'
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
                // Rename pt-100-temperature-* -> Temperature n
                if (str_starts_with($key, 'pt-100-temperature-')) {
                    $num = str_replace('pt-100-temperature-', '', $key);
                    $renamed["Temperature $num"] = $val;
                }
                // Rename thm-30md-humidity-* -> Humidity n
                elseif (str_starts_with($key, 'thm-30md-humidity-')) {
                    $num = str_replace('thm-30md-humidity-', '', $key);
                    $renamed["Humidity $num"] = $val;
                }
            }

            // Format time jadi local readable
            if (isset($assoc['time'])) {
                $renamed['time'] = \Carbon\Carbon::parse($assoc['time'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y H:i:s');
            }

            return $renamed;
        }, $values);

        // Ambil data terakhir untuk info "Last Update"
        $lastRow = end($rows);

        // Ambil daftar mac address untuk dropdown/menu
        $sites = Site::pluck('mac_address')->toArray();

        // Label mapping dipindah ke controller
        $labels = [
            'Temperature 1' => 'Temperature Sensor 1 (°C)',
            'Temperature 2' => 'Temperature Sensor 2 (°C)',
            'Temperature 3' => 'Temperature Sensor 3 (°C)',
            'Temperature 4' => 'Temperature Sensor 4 (°C)',
            'Temperature 5' => 'Temperature Sensor 5 (°C)',
            'Temperature 6' => 'Temperature Sensor 6 (°C)',
            'Temperature 7' => 'Temperature Sensor 7 (°C)',
            'Temperature 8' => 'Temperature Sensor 8 (°C)',
            'Temperature 9' => 'Temperature Sensor 9 (°C)',
            'Temperature 10' => 'Temperature Sensor 10 (°C)',
            'Temperature 11' => 'Temperature Sensor 11 (°C)',
            'Temperature 12' => 'Temperature Sensor 12 (°C)',
            'Temperature 13' => 'Temperature Sensor 13 (°C)',
            'Temperature 14' => 'Temperature Sensor 14 (°C)',
            'Humidity 1'    => 'Humidity Sensor 1 (%RH)',
            'Humidity 2'    => 'Humidity Sensor 2 (%RH)',
            'Humidity 3'    => 'Humidity Sensor 3 (%RH)',
            'Humidity 4'    => 'Humidity Sensor 4 (%RH)',
        ];

        return view('sensors.charts', [
            'mac_address'   => $mac_address,
            'rows'          => $rows,
            'range'         => $range,
            'lastRow'       => $lastRow,
            'macs_menu_map' => $sites,
            'labels'        => $labels,
        ]);
    }
}
