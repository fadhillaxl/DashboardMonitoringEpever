<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relay;
use App\Models\Site;

class RelayController extends Controller
{
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
}
