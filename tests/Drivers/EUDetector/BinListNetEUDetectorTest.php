<?php

namespace Tests\Drivers\EUDetector;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Letux\PayServiceTest\Drivers\EUDetector\BinListNetEUDetector;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BinListNetEUDetectorTest extends TestCase
{
    #[Test]
    public function wrong_return_code(): void
    {
        $mock = new MockHandler([
            new Response(404),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new BinListNetEUDetector($client);

        $this->expectException(\RuntimeException::class);

        $detector->isEU('123');
    }

    #[Test]
    public function wrong_json(): void
    {
        $mock = new MockHandler([
            new Response(200, [], 'wrong json'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new BinListNetEUDetector($client);

        $this->expectException(\RuntimeException::class);

        $detector->isEU('123');
    }

    #[Test]
    public function wrong_country(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "KZ"}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new BinListNetEUDetector($client);

        $this->assertFalse($detector->isEU('123'));
    }

    #[Test]
    public function correct_country(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"country": {"alpha2": "DE"}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $detector = new BinListNetEUDetector($client);

        $this->assertTrue($detector->isEU('123'));
    }
}
