<?php

namespace Letux\PayServiceTest\Drivers\EUDetector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\RuntimeException;

final readonly class BinListNetEUDetector implements EUDetector
{
    private const API_URL = 'https://lookup.binlist.net/';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function isEU(string $bin): bool
    {
        try {
            $response = $this->client->request('GET', self::API_URL . $bin, [
                'headers' => [
                    'Accept-Version' => 3,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': ' . $e->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': ' . $response->getReasonPhrase());
        }

        if (!json_validate($response->getBody()->getContents())) {
            throw new RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': invalid JSON');
        }

        $data = json_decode($response->getBody()->getContents());

        if (!isset($data->country->alpha2)) {
            throw new RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': country not found');
        }

        return in_array($data->country->alpha2, self::EU_COUNTRIES);
    }
}