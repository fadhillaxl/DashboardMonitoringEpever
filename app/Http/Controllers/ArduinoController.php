<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\InfluxService;
use Illuminate\Http\Request;

class ArduinoController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    // Tampilkan data Epever di Blade
    // Tampilkan data Arduino di Blade
    public function showSite($mac_address)
    {
        $result = $this->influx->query(
            "SELECT * FROM sensor_arduino WHERE mac_address='{$mac_address}' ORDER BY time DESC LIMIT 1"
        );

        $series = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'][0] ?? [];

        $data = array_combine($columns, $values);

        // Ambil time
        $time = $data['time'] ?? null; // time dalam format UTC InfluxDB
        if ($time) {
            $time = \Carbon\Carbon::parse($time)->setTimezone('Asia/Jakarta');
        }

        $macs_menu_map = Site::pluck('mac_address')->toArray();

        // --- Konfigurasi section pindah ke controller ---
        $lightSection = [
            'Light Sensor' => [
                'Status' => ['light_status', 'bi-lightbulb'],
                'Lux'    => ['light_lux', 'bi-brightness-high'],
            ],
        ];

        $anemometerSections = [
            'Anemometer Sensor 1' => [
                'm/s'  => ['anemometer_1_mps', 'bi-wind'],
                'km/h' => ['anemometer_1_kph', 'bi-wind'],
            ],
            'Anemometer Sensor 2' => [
                'm/s'  => ['anemometer_2_mps', 'bi-wind'],
                'km/h' => ['anemometer_2_kph', 'bi-wind'],
            ],
        ];

        $pressureSections = [
            'Pressure Sensor 1' => [
                'Bar' => ['pressure_1_bar', 'bi-speedometer'],
                'PSI' => ['pressure_1_psi', 'bi-speedometer'],
                'Pascal' => ['pressure_1_pascal', 'bi-speedometer'],
            ],
            'Pressure Sensor 2' => [
                'Bar' => ['pressure_2_bar', 'bi-speedometer'],
                'PSI' => ['pressure_2_psi', 'bi-speedometer'],
                'Pascal' => ['pressure_2_pascal', 'bi-speedometer'],

            ],
            'Pressure Sensor 3' => [
                'Bar' => ['pressure_3_bar', 'bi-speedometer'],
                'PSI' => ['pressure_3_psi', 'bi-speedometer'],
                'Pascal' => ['pressure_3_pascal', 'bi-speedometer'],
            ],
            'Pressure Sensor 4' => [
                'Bar' => ['pressure_4_bar', 'bi-speedometer'],
                'PSI' => ['pressure_4_psi', 'bi-speedometer'],
                'Pascal' => ['pressure_4_pascal', 'bi-speedometer'],
            ],
        ];

        $booleanFields = ['light_status'];

        return view('arduino.index', compact(
            'mac_address',
            'data',
            'time',
            'macs_menu_map',
            'lightSection',
            'anemometerSections',
            'pressureSections',
            'booleanFields'
        ));
    }

    public function showCharts($mac_address, Request $request)
    {
        $range = $request->get('range', '15m');
        $now = now()->toImmutable();

        // Mapping range ke waktu start
        $ranges = [
            '15m' => fn() => $now->subMinutes(15),
            '1h'  => fn() => $now->subHour(),
            '1d'  => fn() => $now->subDay(),
            '1w'  => fn() => $now->subWeek(),
            '1m'  => fn() => $now->subMonth(),
            '1y'  => fn() => $now->subYear(),
        ];
        $start = ($ranges[$range] ?? $ranges['1h'])();

        // Query InfluxDB
        $query = "
        SELECT *
        FROM sensor_arduino
        WHERE mac_address = '$mac_address' AND time >= '{$start->toIso8601String()}'
        ORDER BY time ASC
    ";
        $result = $this->influx->query($query);

        $series = $result['results'][0]['series'][0] ?? null;
        $columns = $series['columns'] ?? [];
        $values  = $series['values'] ?? [];

        // Convert rows ke associative + format time
        $rows = array_map(function ($row) use ($columns) {
            $assoc = array_combine($columns, $row);

            if (isset($assoc['time'])) {
                $assoc['time'] = \Carbon\Carbon::parse($assoc['time'])
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y H:i:s');
            }

            return $assoc;
        }, $values);

        // Ambil last row
        $lastRow = end($rows);

        // Mapping nama kolom ke label chart
        $labels = [
            'light_status' => 'Light Status (ON/OFF)',
            'light_lux' => 'Light Intensity (Lux)',
            'anemometer_1_mps' => 'Anemometer 1 (m/s)',
            'anemometer_1_kph' => 'Anemometer 1 (km/h)',
            'anemometer_2_mps' => 'Anemometer 2 (m/s)',
            'anemometer_2_kph' => 'Anemometer 2 (km/h)',
            'pressure_1_bar' => 'Pressure 1 (Bar)',
            'pressure_1_psi' => 'Pressure 1 (PSI)',
            'pressure_1_pascal' => 'Pressure 1 (Pa)',
            'pressure_2_bar' => 'Pressure 2 (Bar)',
            'pressure_2_psi' => 'Pressure 2 (PSI)',
            'pressure_2_pascal' => 'Pressure 2 (Pa)',
            'pressure_3_bar' => 'Pressure 3 (Bar)',
            'pressure_3_psi' => 'Pressure 3 (PSI)',
            'pressure_3_pascal' => 'Pressure 3 (Pa)',
            'pressure_4_bar' => 'Pressure 4 (Bar)',
            'pressure_4_psi' => 'Pressure 4 (PSI)',
            'pressure_4_pascal' => 'Pressure 4 (Pa)',
        ];

        // Hanya ambil kolom valid (skip time & mac_address)
        $chartColumns = array_filter($columns, fn($col) => !in_array($col, ['time', 'mac_address']));

        $macs_menu_map = Site::pluck('mac_address')->toArray();

        return view('arduino.charts', compact(
            'mac_address',
            'rows',
            'range',
            'lastRow',
            'macs_menu_map',
            'labels',
            'chartColumns'
        ));
    }
}
