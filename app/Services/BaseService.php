<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

abstract class BaseService
{
    protected $client;
    private int $timeout = 60;

    public function __construct()
    {
        $this->client = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9,pt;q=0.8',
            'Authorization' => 'Guest',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ])->timeout($this->timeout);
    }
}
