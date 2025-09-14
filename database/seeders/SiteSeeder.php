<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    public function run()
    {
        Site::create([
            'nama_site' => 'Site A',
            'alamat_lengkap' => 'Jl. Contoh No.1',
            'lokasi' => 'Jakarta',
            'pic' => 'Juliano',
            'mac_address' => '00:0E:49:80:70:11',
        ]);

        Site::create([
            'nama_site' => 'Site B',
            'alamat_lengkap' => 'Jl. Contoh No.2',
            'lokasi' => 'Bandung',
            'pic' => 'Budi',
            'mac_address' => '00:0E:49:80:70:12',
        ]);
    }
}
