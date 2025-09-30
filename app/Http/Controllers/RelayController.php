<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relay;
use App\Models\Site;
use App\Services\InfluxService;

class RelayController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    // Halaman kontrol relay
    public function controlPage($mac_address)
    {
        $site  = Site::where('mac_address', $mac_address)->firstOrFail();
        $relay = Relay::where('id_site', $site->id)->first();
        $macs_menu_map = Site::pluck('mac_address')->toArray();

        // Parse relay_command dan relay_condition langsung di controller
        $relay_command = is_array($relay->relay_command)
            ? $relay->relay_command
            : (array) json_decode($relay->relay_command, true);

        $relay_condition = is_array($relay->relay_condition)
            ? $relay->relay_condition
            : (array) json_decode($relay->relay_condition, true);

        // Fallback default kalau masih kosong/null
        $relay_command   = $relay_command ?: array_fill(0, 8, 0);
        $relay_condition = $relay_condition ?: array_fill(0, 8, 0);

        return view('relay.control', compact(
            'mac_address',
            'macs_menu_map',
            'relay',
            'relay_command',
            'relay_condition'
        ));
    }

    // Update salah satu channel relay
    public function updateCommand(Request $request, $id)
    {
        $request->validate([
            'channel' => 'required|integer|min:0|max:7', // nomor channel 0-7
            'state'   => 'required|integer|in:0,1',      // nilai 0/1
        ]);

        try {
            $relay = Relay::findOrFail($id);

            // ambil command lama (array) atau isi default
            $commands = $relay->relay_command ?? array_fill(0, 8, 0);

            // pastikan semua integer
            $commands = array_map('intval', $commands);

            // update channel tertentu
            $commands[$request->channel] = (int) $request->state;

            // simpan kembali (Laravel otomatis json_encode karena cast array)
            $relay->relay_command = $commands;
            $relay->save();

            return redirect()->back()->with('success', 'Relay command updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
        SELECT *
        FROM relay_data
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

        return view('relay.charts', compact(
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
