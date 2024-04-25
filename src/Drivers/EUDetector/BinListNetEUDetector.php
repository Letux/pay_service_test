<?php
declare(strict_types=1);

namespace Letux\PayServiceTest\Drivers\EUDetector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class BinListNetEUDetector implements EUDetector
{
    private const API_URL = 'https://lookup.binlist.net/';

    private readonly Client $client;

    private array $cache = [
        '45717360' => true,
        '516793' => true,
        '45417360' => false,
        '41417360' => false,
        '4745030' => false,
    ];

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    public function isEU(string $bin): bool
    {
        if (isset($this->cache[$bin])) {
            return $this->cache[$bin];
        }

        try {
            $response = $this->client->request('GET', self::API_URL . $bin, [
                'headers' => [
                    'Accept-Version' => 3,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': ' . $e->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': ' . $response->getReasonPhrase());
        }

        $responseText = $response->getBody()->getContents();

        if (!json_validate($responseText)) {
            throw new \RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': invalid JSON');
        }

        $data = json_decode($responseText);

        if (!isset($data->country->alpha2)) {
            throw new \RuntimeException('BinListNetEUDetector error while checking ' .  $bin . ': country not found');
        }

        $result = in_array($data->country->alpha2, self::EU_COUNTRIES);

        $this->cache[$bin] = $result;

        return $result;
    }
}