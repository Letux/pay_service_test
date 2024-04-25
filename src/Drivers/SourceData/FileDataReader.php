<?php
declare(strict_types=1);

namespace Letux\PayServiceTest\Drivers\SourceData;

use Letux\PayServiceTest\DTOs\TransactionDTO;

final readonly class FileDataReader implements SourceData
{
    public function __construct(private string $filename)
    {
    }

    /**
     * @return iterable<TransactionDTO>
     */
    public function getTransactions(): iterable
    {
        $handle = fopen($this->filename, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Cannot open file');
        }

        $i = 0;
        while (!feof($handle)) {
            $i++;
            $row = trim(fgets($handle));

            if ($row === '') {
                continue;
            }

            if (!json_validate($row)) {
                throw new \RuntimeException('Invalid JSON in line ' . $i);
            }

            $row = json_decode($row);

            if (!isset($row->bin, $row->amount, $row->currency)) {
                throw new \RuntimeException('Invalid JSON structure in line ' . $i);
            }

            yield new TransactionDTO($row->bin, $row->amount, $row->currency);
        }

        fclose($handle);
    }
}