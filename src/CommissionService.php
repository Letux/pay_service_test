<?php
declare(strict_types=1);

namespace Letux\PayServiceTest;

use Letux\PayServiceTest\Drivers\EUDetector\EUDetector;
use Letux\PayServiceTest\Drivers\Rate\Rate;
use Letux\PayServiceTest\Drivers\SourceData\SourceData;
use Letux\PayServiceTest\DTOs\TransactionDTO;

final readonly class CommissionService
{
    private const float EU_MULTIPLIER = 0.01;
    private const float NON_EU_MULTIPLIER = 0.02;

    public function __construct(
        private SourceData $reader,
        private Rate $rate,
        private EUDetector $euDetector
    )
    {
    }

    public function handle(): void
    {
        $transactions = $this->reader->getTransactions();

        foreach ($transactions as $transaction) {
            echo $this->getTransactionCommission($transaction) . PHP_EOL;
        }
    }

    private function getTransactionCommission(TransactionDTO $transaction): float
    {
        $rate = $this->rate->getRate($transaction->currency);

        $amountInEUR = $this->getAmountInEUR($transaction->amount, $rate, $transaction->currency);

        $isEU = $this->euDetector->isEU($transaction->bin);

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
}