<?php

namespace Seatsio\HoldTokens;

use Seatsio\SeatsioJsonMapper;

class HoldTokens
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @return HoldToken
     */
    public function create()
    {
        $res = $this->client->post('/hold-tokens');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    /**
     * @param $holdToken string
     * @param $expiresInMintes int
     * @return HoldToken
     */
    public function update($holdToken, $expiresInMintes)
    {
        $request = new \stdClass();
        $request->expiresInMinutes = $expiresInMintes;
        $res = $this->client->post('/hold-tokens/' . $holdToken, ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

    /**
     * @param $holdToken string
     * @return HoldToken
     */
    public function retrieve($holdToken)
    {
        $res = $this->client->get('/hold-tokens/' . $holdToken);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new HoldToken());
    }

}