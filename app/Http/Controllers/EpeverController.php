<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Services\InfluxService;

class EpeverController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    // Tampilkan data Epever di Blade
    public function showSite($mac_address)
    {
        $result = $this->influx->query(
            "SELECT * FROM epever_data WHERE mac_address='{$mac_address}' ORDER BY time DESC LIMIT 1"
        );

        $series = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'][0] ?? [];

        $data = array_combine($columns, $values);

        // Ambil time
        $time = $data['time'] ?? null; // time dalam format UTC InfluxDB
        if ($time) {
            $time = \Carbon\Carbon::parse($time)->setTimezone('Asia/Jakarta'); // ubah timezone jika perlu
        }

        $macs_menu_map = Site::pluck('mac_address')->toArray();

        // Semua konfigurasi pindah ke sini
        $sections = [
            'PV' => [
                'icon' => 'bi-sun text-warning',
                'fields' => [
                    'PV Voltage' => 'pv_voltage',
                    'PV Current' => 'pv_current',
                    'PV Power' => 'pv_power',
                    'PV Max Voltage Today' => 'pv_max_voltage_today',
                    'PV Min Voltage Today' => 'pv_min_voltage_today',
                ],
            ],
            'Battery' => [
                'icon' => 'bi-battery-full text-success',
                'fields' => [
                    'Battery Voltage' => 'battery_voltage',
                    'Battery Current' => 'battery_current',
                    'Battery Power' => 'battery_power',
                    'Battery Percentage' => 'battery_percentage',
                    'Battery Temp' => 'battery_temperature',
                    'Battery Max Voltage Today' => 'battery_max_voltage_today',
                    'Battery Min Voltage Today' => 'battery_min_voltage_today',
                ],
            ],
            'Load' => [
                'icon' => 'bi-lightning text-primary',
                'fields' => [
                    'Load Voltage' => 'load_voltage',
                    'Load Current' => 'load_current',
                    'Load Power' => 'load_power',
                    'Input Voltage Status' => 'input_voltage_status',
                    'Output Power Load' => 'output_power_load',
                ],
            ],
            'Energy' => [
                'icon' => 'bi-graph-up text-info',
                'fields' => [
                    'Energy Generated Today' => 'energy_generated_today',
                    'Energy Generated This Month' => 'energy_generated_month',
                    'Energy Generated This Year' => 'energy_generated_year',
                    'Energy Generated Total' => 'energy_generated_total',
                    'Energy Consumed Today' => 'energy_consumed_today',
                    'Energy Consumed This Month' => 'energy_consumed_month',
                    'Energy Consumed This Year' => 'energy_consumed_year',
                    'Energy Consumed Total' => 'energy_consumed_total',
                ],
            ],
            'Status' => [
                'icon' => 'bi-activity text-secondary',
                'fields' => [
                    'Charging Status' => 'charging_status',
                    'Is Day' => 'is_day',
                    'Is Night' => 'is_night',
                    'Device Over Temp' => 'device_over_temperature',
                ],
            ],
        ];

        $booleanFields = [
            'charging_status',
            'is_day',
            'is_night',
            'device_over_temperature',
        ];

        $stringStatusFields = [
            'input_voltage_status',
            'output_power_load',
        ];

        $unitMap = [
            'Voltage' => 'V',
            'Current' => 'A',
            'Power' => 'W',
            'Temp' => '°C',
            'Percentage' => '%',
            'Energy' => 'kWh',
        ];

        $statusColors = [
            'Normal' => 'bg-success',
            'High' => 'bg-danger',
            'Low' => 'bg-warning',
        ];

        return view('epever.index', compact(
            'mac_address',
            'data',
            'time',
            'macs_menu_map',
            'sections',
            'booleanFields',
            'stringStatusFields',
            'unitMap',
            'statusColors'
        ));
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
        SELECT pv_voltage, pv_current, pv_power,
               battery_voltage, battery_current, battery_power,
               load_voltage, load_current, load_power
        FROM epever_data
        WHERE mac_address = '$mac_address'
          AND time >= '{$start->toIso8601String()}'
          AND time <= '{$end->toIso8601String()}'
        ORDER BY time ASC
    ";
        $result = $this->influx->query($query);

        $series  = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'] ?? [];

        // Format data ke associative array
        $rows = array_map(function ($row) use ($columns) {
            $assoc = array_combine($columns, $row);
            if (isset($assoc['time'])) {
                $assoc['time'] = \Carbon\Carbon::parse($assoc['time'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y H:i:s');
            }
            return $assoc;
        }, $values);

        $lastRow = end($rows) ?: [];
        $macs_menu_map = Site::pluck('mac_address')->toArray();
        $chartColumns  = array_filter($columns, fn($c) => !in_array($c, ['time', 'mac_address']));
        $availableRanges = ['15m', '1h', '1d', '1w', '1m', '1y'];

        return view('epever.charts', compact(
            'mac_address',
            'rows',
            'range',
            'lastRow',
            'macs_menu_map',
            'availableRanges',
            'chartColumns',
            'startDate',
            'endDate'
        ));
    }
}
