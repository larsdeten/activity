<?php
namespace App\Http\Libs\Api;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class Base
{

    protected $httpClient = null;

    protected $httpImageClient = null;

    public function __construct($token)
    {
        $this->httpClient = Http::withToken($token)->withHeaders([
            'Accept' => 'application/json'
        ])->withoutVerifying();

        $this->httpImageClient = Http::withToken($token)->withoutVerifying();
    }

    protected function prepare_params($method, $params = [])
    {
        return [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => time()
        ];
    }
}
