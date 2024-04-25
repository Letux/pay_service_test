<?php

namespace Letux\PayServiceTest\Drivers\SourceData;

use Letux\PayServiceTest\DTOs\TransactionDTO;

interface SourceData
{
    /**
     * @return iterable<TransactionDTO>
     */
    public function getData(): iterable;
}