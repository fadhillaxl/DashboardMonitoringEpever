<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Site;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('IOT-API-KEY');
        $mac = $request->input('mac_address'); // ambil dari body request

        if ($key !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key'], 401);
        }

        if (!$mac || !Site::where('mac_address', $mac)->exists()) {
            return response()->json(['error' => 'Unauthorized: MAC Address not registered'], 401);
        }

        return $next($request);
    }
}
