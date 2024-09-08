<?php

namespace App\Service;

use App\Enum\EUCountry;
use App\Model\Transaction;

readonly class TransactionProcessor
{
    public function __construct(
        private BinFetcher $binFetcher,
        private CurrencyService $currencyService
    )
    {
    }

    /**
     * @param Transaction[] $transactions
     * @return array<int, float>
     * @throws \Exception
     */
    public function processTransactions(array $transactions): array
    {
        $fees = [];

        foreach ($transactions as $transaction) {
            $fees[] = $this->processTransaction($transaction);
        }

        return $fees;
    }

    /**
     * @param Transaction $transaction
     * @return float
     * @throws \Exception
     */
    public function processTransaction(Transaction $transaction): float
    {
        $countryCode = $this->binFetcher->fetchBinData($transaction->getBin());
        $isEu = EUCountry::isEU($countryCode);
        $amountInEur = $this->currencyService->convertToEur($transaction->getAmount(), $transaction->getCurrency());
        $feePercentage = $isEu ? 0.01 : 0.02;
        return $amountInEur * $feePercentage;
    }
}
