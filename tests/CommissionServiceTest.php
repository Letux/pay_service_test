<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Letux\PayServiceTest\CommissionService;

use Letux\PayServiceTest\Drivers\EUDetector\BinListNetEUDetector;
use Letux\PayServiceTest\Drivers\Rate\ExchangeRatesAPIIO;
use Letux\PayServiceTest\Drivers\SourceData\FileDataReader;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;

final class CommissionServiceTest extends TestCase
{
    #[Test]
    public function get_commission()
    {
        $service = new CommissionService(
            new FileDataReader('input.txt'),
            new ExchangeRatesAPIIO('123'),
            new BinListNetEUDetector()
        );

        $reflector = new ReflectionClass(CommissionService::class);
        $method = $reflector->getMethod('getCommission');
        $method->setAccessible(true);
        $result = $method->invokeArgs($service, [100, true]);
        $this->assertEquals(1, $result);

        $result = $method->invokeArgs($service, [100, false]);
        $this->assertEquals(2, $result);
    }

    #[Test]
    public function amount_in_eur()
    {
        $service = new CommissionService(
            new FileDataReader('input.txt'),
            new ExchangeRatesAPIIO('123'),
            new BinListNetEUDetector()
        );

        $reflector = new ReflectionClass(CommissionService::class);
        $method = $reflector->getMethod('getAmountInEUR');
        $method->setAccessible(true);
        $result = $method->invokeArgs($service, [100, 0.5, 'EUR']);
        $this->assertEquals(100, $result);

        $result = $method->invokeArgs($service, [100, 0.0, 'USD']);
        $this->assertEquals(100, $result);

        $result = $method->invokeArgs($service, [100, 0.5, 'USD']);
        $this->assertEquals(200, $result);
    }

    #[Test]
    public function handle()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"success": true, "rates": {"EUR": 1, "USD": 1.2, "JPY": 0.8}}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new CommissionService(
            new FileDataReader(__DIR__ . '/input.txt'),
            new ExchangeRatesAPIIO('123', $client),
            new BinListNetEUDetector()
        );

        $this->expectOutputString("1.00
0.42
250.00
");

        $service->handle();
    }
}
