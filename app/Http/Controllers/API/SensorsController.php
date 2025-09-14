<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SensorDataReceiveRequest;
use App\Services\InfluxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SensorsController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    // API: insert data
    public function store(SensorDataReceiveRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->influx->write(
                $data['type'],
                $data['sensors'],
                ['mac_address' => $data['mac_address']]
            );

            return response()->json([
                'success' => true,
                'message' => "Data Tersimpan",
            ], 201);
        } catch (Throwable $error) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error'   => "Server Error",
                'error-data'   => $error->getMessage(),
            ], 500);
        }
    }
}
