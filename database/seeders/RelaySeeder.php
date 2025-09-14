<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Relay;
use App\Models\Site;

class RelaySeeder extends Seeder
{
    public function run()
    {
        $sites = Site::all();

        foreach ($sites as $site) {
            Relay::create([
                'id_site' => $site->id,
                'relay_connection' => 0,
                'relay_condition' => 0,
                'relay_command' => 0,
            ]);
        }
    }
}
