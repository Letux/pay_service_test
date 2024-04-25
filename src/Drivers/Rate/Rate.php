<?php

namespace Letux\PayServiceTest\Drivers\Rate;

interface Rate
{
    public function getRate(string $currency): float;
}