<?php
declare(strict_types=1);

use Letux\PayServiceTest\Drivers\SourceData\FileDataReader;
use Letux\PayServiceTest\Drivers\SourceData\SourceData;

require_once __DIR__ . '/vendor/autoload.php';

if ($argc < 2) {
    echo "Usage: php app.php <filename>\n";
    exit(1);
}

processData(new FileDataReader($argv[1]));

function processData(SourceData $reader) {
    foreach ($reader->getData() as $data) {
        // Обработка данных
        var_dump($data);
    }
}