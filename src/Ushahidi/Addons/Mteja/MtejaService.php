<?php
namespace Ushahidi\Addons\Mteja;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MtejaService
{
    protected $baseUrl = 'https://api.sentry.mteja.io/api/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false
        ]);
    }


    public function request(string $method, $url, $data)
    {
        try {
            $response = $this->client->request($method, $url, $data);
            return $this->formatResponse($response);
        } catch (RequestException $e) {
            dump($e);
        }
    }

    public function formatResponse($response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
