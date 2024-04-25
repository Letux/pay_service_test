<?php
declare(strict_types=1);

namespace Letux\PayServiceTest;

final readonly class SourceDataDTO
{
    public function __construct(
        public string $bin,
        public string $amount,
        public string $currency,
    )
    {
    }
}