<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_site',
        'alamat_lengkap',
        'lokasi',
        'pic',
        'mac_address',
    ];

    // Relasi ke Relay
    public function relay()
    {
        return $this->hasOne(Relay::class, 'id_site');
    }

    protected static function booted()
    {
        static::created(function ($site) {
            $site->relay()->create([
                'relay_connection' => 0,
                'relay_condition'  => array_fill(0, 8, 0),
                'relay_command'    => array_fill(0, 8, 0),
            ]);
        });
    }
}
