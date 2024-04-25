<?php
declare(strict_types=1);

namespace Letux\PayServiceTest\Drivers\Rate;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final readonly class ExchangeRatesAPIIO implements Rate
{
    private const string API_URL = 'https://api.apilayer.com/exchangerates_data/latest';
    private const string DEFAULT_CURRENCY = 'EUR';

    private Client $client;

    public function __construct(private string $token)
    {
        $this->client = new Client();
    }

    public function getRate(string $currency): float
    {
        try {
            $response = $this->client->request('GET', self::API_URL, [
                'query' => [
                    'base' => self::DEFAULT_CURRENCY,
                ],
                'headers' => [
                    'Content-Type' => 'text/plain',
                    'apikey' => $this->token,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('api.exchangeratesapi.io error: ' . $response->getReasonPhrase());
        }

        $responseText = $response->getBody()->getContents();

        if (!json_validate($responseText)) {
            throw new \RuntimeException('api.exchangeratesapi.io error: invalid JSON');
        }

        $data = json_decode($responseText);

        if (!isset($data->success) || $data->success === false) {
            throw new \RuntimeException('api.exchangeratesapi.io error: ' . $data->error->info);
        }

        if (!isset($data->rates->{$currency})) {
            throw new \RuntimeException('api.exchangeratesapi.io error: currency not found');
        }

        return $data->rates->{$currency};
    }
}