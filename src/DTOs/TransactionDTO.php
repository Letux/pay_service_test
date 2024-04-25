<?php
declare(strict_types=1);

namespace Letux\PayServiceTest\DTOs;

final readonly class TransactionDTO
{
    public float $amount;

    public function __construct(
        public string $bin,
        string $amount,
        public string $currency,
    )
    {
        $this->amount = (float) $amount;
    }
}