<?php

namespace App\Service;

use App\Enum\EUCountry;
use App\Model\Transaction;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
     * @return array<int, ?float>
     * @throws TransportExceptionInterface
     */
    public function processTransactions(array $transactions): array
    {
        $commissions = [];

        foreach ($transactions as $transaction) {
            $commissions[] = $this->processTransaction($transaction) ?? 'No data';
        }

        return $commissions;
    }

    /**
     * @param Transaction $transaction
     * @return null|float
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function processTransaction(Transaction $transaction): ?float
    {
        $countryCode = $this->binFetcher->fetchCountryCode($transaction->getBin());
        if ($countryCode === null) {
            return null;
        }

        $isEu = EUCountry::isEU($countryCode);
        $amountInEur = $this->currencyService->convertToEur($transaction->getAmount(), $transaction->getCurrency());
        $feePercentage = $isEu ? 0.01 : 0.02;
        return round($amountInEur * $feePercentage, 2);
    }
}
