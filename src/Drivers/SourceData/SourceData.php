<?php

namespace Letux\PayServiceTest\Drivers\SourceData;

interface SourceData
{
    public function getData(): iterable;
}