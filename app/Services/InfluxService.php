<?php

namespace App\Services;

use GuzzleHttp\Client;

class InfluxService
{
    protected $client;
    protected $host;
    protected $port;
    protected $db;
    protected $user;
    protected $pass;

    public function __construct()
    {
        $this->host = env('INFLUX_HOST', 'http://localhost');
        $this->port = env('INFLUX_PORT', 8086);
        $this->db   = env('INFLUX_DB', 'solar');
        $this->user = env('INFLUX_USER', 'grafana');
        $this->pass = env('INFLUX_PASS', 'solar');

        $this->client = new Client([
            'base_uri' => "{$this->host}:{$this->port}",
            'timeout'  => 5,
        ]);
    }

    // Insert data
    public function write($measurement, $fields, $tags = [])
    {
        $line = $measurement;

        if (!empty($tags)) {
            $tagString = [];
            foreach ($tags as $k => $v) {
                $tagString[] = "$k=$v";
            }
            $line .= ',' . implode(',', $tagString);
        }

        $fieldString = [];
        foreach ($fields as $k => $v) {
            if (is_string($v)) {
                $fieldString[] = "$k=\"$v\"";
            } else {
                $fieldString[] = "$k=$v";
            }
        }
        $line .= ' ' . implode(',', $fieldString);

        return $this->client->post("/write", [
            'query' => [
                'db' => $this->db,
                'u'  => $this->user,
                'p'  => $this->pass,
            ],
            'body' => $line,
        ]);
    }

    // Query data
    public function query($q)
    {
        $res = $this->client->get("/query", [
            'query' => [
                'db' => $this->db,
                'u'  => $this->user,
                'p'  => $this->pass,
                'q'  => $q,
            ]
        ]);

        return json_decode($res->getBody(), true);
    }
}
