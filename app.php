<?php
declare(strict_types=1);

use Letux\PayServiceTest\CommissionService;
use Letux\PayServiceTest\Drivers\SourceData\FileDataReader;

require_once __DIR__ . '/vendor/autoload.php';

if ($argc < 2) {
    echo "Usage: php app.php <filename>\n";
    exit(1);
}

$service = new CommissionService(
    new FileDataReader($argv[1])
);

$service->handle();