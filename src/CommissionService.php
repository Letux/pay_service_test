<?php

namespace Letux\PayServiceTest;

use Letux\PayServiceTest\Drivers\Rate\Rate;
use Letux\PayServiceTest\Drivers\SourceData\SourceData;
use Letux\PayServiceTest\DTOs\TransactionDTO;

final readonly class CommissionService
{
    private const EU_MULTIPLIER = 0.01;
    private const NON_EU_MULTIPLIER = 0.02;

    public function __construct(
        private SourceData $reader,
        private Rate $rate,

    )
    {
    }

    public function handle(): void
    {
        $transactions = $this->reader->getData();

        foreach ($transactions as $transaction) {
            echo $this->getTransactionCommission($transaction) . PHP_EOL;
        }
    }

    private function getTransactionCommission(TransactionDTO $transaction): float
    {
        $rate = $this->rate->getRate($transaction->currency);

        $amountInEUR = $this->getAmountInEUR($transaction->amount, $rate, $transaction->currency);

        $isEU = $this->isEU($transaction->bin);

        return $this->getCommission($amountInEUR, $isEU);
    }

    private function getAmountInEUR(float $amount, float $rate, string $currency): float
    {
        if ($currency === 'EUR' || $rate === 0.0) {
            return $amount;
        }

        return $amount / $rate;
    }

    private function getCommission(float $amountInEUR, bool $isEu): float
    {
        return $amountInEUR * ($isEu ? self::EU_MULTIPLIER : self::NON_EU_MULTIPLIER);
    }

    private function isEU(string $bin)
    {
        $binResults = file_get_contents('https://lookup.binlist.net/' .$bin);
        if (!$binResults)
            die('error!');
        $r = json_decode($binResults);
        return in_array($r->country->alpha2, self::EU_COUNTRIES);
    }
}