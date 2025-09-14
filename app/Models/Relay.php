<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relay extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'id_site',
        'relay_connection',
        'relay_condition',
        'relay_command',
        'update_from_site'
    ];

    protected $casts = [
        'update_from_site' => 'datetime',
        'relay_condition' => 'array',
        'relay_command'   => 'array',
    ];

    // Relasi ke Site
    public function site()
    {
        return $this->belongsTo(Site::class, 'id_site');
    }
}
