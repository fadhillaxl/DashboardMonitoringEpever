<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Site;
use Illuminate\Support\Facades\Cache;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek API key dulu
        $key = $request->header('IOT-API-KEY');
        if ($key !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key'], 401);
        }

        // Ambil mac_address dari body
        $mac = $request->input('mac_address');
        if (!$mac) {
            return response()->json(['error' => 'Unauthorized: MAC Address required'], 401);
        }

        // Cache daftar MAC selama 5 menit
        $allowedMacs = Cache::remember('allowed_macs', now()->addMinutes(5), function () {
            return Site::pluck('mac_address')->toArray();
        });

        // Cek apakah MAC terdaftar
        if (!in_array($mac, $allowedMacs)) {
            return response()->json(['error' => 'Unauthorized: MAC Address not registered'], 401);
        }

        return $next($request);
    }
}
