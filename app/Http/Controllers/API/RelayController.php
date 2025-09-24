<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Relay;
use App\Models\Site;
use App\Services\InfluxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelayController extends Controller
{
    protected $influx;

    public function __construct(InfluxService $influx)
    {
        $this->influx = $influx;
    }

    // POST API: update relay status (8 channel sekaligus)
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required|string',
            'relay_connection' => 'required|integer|in:0,1',
            'relay_condition' => 'required|array|size:8',
            'relay_condition.*' => 'integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $site = Site::where('mac_address', $request->mac_address)->firstOrFail();

            Relay::updateOrCreate(
                ['id_site' => $site->id],
                [
                    'relay_connection' => $request->relay_connection,
                    'relay_condition'  => json_encode($request->relay_condition),
                    'update_from_site' => Carbon::now(),
                ]
            );
            
            $fields = [];
            foreach ($request->relay_condition as $i => $val) {
                $fields["relay_$i"] = (int) $val;
            }
            $this->influx->write(
                "relay_data",
                $fields,
                ['mac_address' => $request->mac_address],
            );

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // GET API: ambil command untuk 8 channel relay
    public function getCommand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $site  = Site::where('mac_address', $request->mac_address)->firstOrFail();
            $relay = Relay::where('id_site', $site->id)->first();

            return response()->json([
                'relay_command' => $relay?->relay_command ?? array_fill(0, 8, 0),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
