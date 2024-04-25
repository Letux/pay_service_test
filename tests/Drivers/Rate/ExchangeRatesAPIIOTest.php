<?php

namespace Tests\Drivers\Rate;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Letux\PayServiceTest\Drivers\EUDetector\BinListNetEUDetector;
use Letux\PayServiceTest\Drivers\Rate\ExchangeRatesAPIIO;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExchangeRatesAPIIOTest extends TestCase
{
    #[Test]
    public function wrong_return_code(): void
    {
        $mock = new MockHandler([
            new Response(404),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new ExchangeRatesAPIIO('token', $client);

        $this->expectException(\RuntimeException::class);

        $detector->getRate('USD');
    }

    #[Test]
    public function wrong_json(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'wrong json'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new ExchangeRatesAPIIO('token', $client);

        $this->expectException(\RuntimeException::class);

        $detector->getRate('USD');
    }

    #[Test]
    public function api_error(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success": false, "rates": {"USD": 1.2}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new ExchangeRatesAPIIO('token', $client);

        $this->expectException(\RuntimeException::class);

        $detector->getRate('USD');
    }

    #[Test]
    public function wrong_currency(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success": true, "rates": {"USD": 1.2}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new ExchangeRatesAPIIO('token', $client);

        $this->expectException(\RuntimeException::class);

        $detector->getRate('EUR');
    }

    #[Test]
    public function correct_currency(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success": true, "rates": {"USD": 1.2}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new ExchangeRatesAPIIO('token', $client);

        $this->assertEquals(1.2, $detector->getRate('USD'));
    }
}
