<?php
declare(strict_types=1);

use Letux\PayServiceTest\CommissionService;
use Letux\PayServiceTest\Drivers\EUDetector\BinListNetEUDetector;
use Letux\PayServiceTest\Drivers\Rate\ExchangeRatesAPIIO;
use Letux\PayServiceTest\Drivers\SourceData\FileDataReader;

require_once __DIR__ . '/vendor/autoload.php';

if ($argc < 2) {
    echo "Usage: php app.php <filename>\n";
    exit(1);
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required(['API_LAYER_TOKEN']);

$service = new CommissionService(
    new FileDataReader($argv[1]),
    new ExchangeRatesAPIIO(getenv('API_LAYER_TOKEN')),
    new BinListNetEUDetector()
);

$service->handle();