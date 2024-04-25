<?php

namespace Tests\Drivers\SourceData;

use Letux\PayServiceTest\Drivers\SourceData\FileDataReader;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileDataReaderTest extends TestCase
{
    #[Test]
    public function wrong_file()
    {
        $filename = __DIR__ . '/wrong_file.txt';

        $this->assertFalse(file_exists($filename));

        $this->expectException(\RuntimeException::class);

        $reader = new FileDataReader($filename);

        foreach ($reader->getTransactions() as $transaction) {
        }
    }

    #[Test]
    public function wrong_json()
    {
        $filename = __DIR__ . '/wrong_json.txt';

        $this->assertTrue(file_exists($filename));

        $reader = new FileDataReader($filename);

        $this->expectException(\RuntimeException::class);

        foreach ($reader->getTransactions() as $transaction) {
        }
    }

    #[Test]
    public function wrong_json_structure()
    {
        $filename = __DIR__ . '/wrong_json_structure1.txt';
        $this->assertTrue(file_exists($filename));
        $reader = new FileDataReader($filename);
        $this->expectException(\RuntimeException::class);
        foreach ($reader->getTransactions() as $transaction) {
        }

        $filename = __DIR__ . '/wrong_json_structure2.txt';
        $this->assertTrue(file_exists($filename));
        $reader = new FileDataReader($filename);
        $this->expectException(\RuntimeException::class);
        foreach ($reader->getTransactions() as $transaction) {
        }

        $filename = __DIR__ . '/wrong_json_structure3.txt';
        $this->assertTrue(file_exists($filename));
        $reader = new FileDataReader($filename);
        $this->expectException(\RuntimeException::class);
        foreach ($reader->getTransactions() as $transaction) {
        }
    }

    #[Test]
    public function correct()
    {
        $filename = __DIR__ . '/input.txt';

        $this->assertTrue(file_exists($filename));

        $reader = new FileDataReader($filename);

        $transactions = iterator_to_array($reader->getTransactions());

        $this->assertCount(5, $transactions);

        $this->assertEquals('45717360', $transactions[0]->bin);
        $this->assertEquals(100, $transactions[0]->amount);
        $this->assertEquals('EUR', $transactions[0]->currency);

        $this->assertEquals('516793', $transactions[1]->bin);
        $this->assertEquals(50, $transactions[1]->amount);
        $this->assertEquals('USD', $transactions[1]->currency);
    }

}
