<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class Accurate_Creds extends Controller
{
    protected $access_token;
    protected $refresh_token;

    public function debugger(){
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://61b6a55fc95dd70017d40f78.mockapi.io/tester/']);
        return $client->request('GET', 'tester', ['verify' => false])->getBody();
    }

    public function request_token_accurate(){
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://account.accurate.id/']);
        $response_1 = json_decode($client->request('POST', 'oauth/token?code=518jsmcQgAo22s7HNdtp&grant_type=authorization_code&redirect_uri=http://147.139.168.202:8888/', [
            'auth' => ['e27143a9-6e84-40a4-9abe-4d5736c6e47d', 'f18e6f4b2195505abf6c1ff96e92ed67'],
            'verify' => false
        ])->getBody());
        $access_token = $response_1->access_token;

        $response_2 = json_decode($client->request('POST', 'api/open-db.do?id=300600', [
            'headers' => [
                'Authorization' => 'Bearer ' . $response_1->access_token,
            ],
            'verify' => false
        ])->getBody());
        $session = $response_2->session;
        return [$access_token, $session];
    }
}
