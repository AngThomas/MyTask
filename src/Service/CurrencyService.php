<?php

namespace App\Service;

use App\Interface\ExchangeRateFetcherInterface;
use Exception;

readonly class CurrencyService
{
    public function __construct(
        private ExchangeRateFetcherInterface $exchangeRateFetcher
    )
    {
    }

    /**
     * @throws Exception
     */
    public function convertToEur(float $amount, string $currency): float
    {
        if ($currency === 'EUR') {
            return $amount;
        }

        $rate = $this->exchangeRateFetcher->fetchExchangeRate($currency);

        if ($rate == 0) {
            throw new Exception("Currency rate for {$currency} not found");
        }

        return $amount / $rate;
    }
}
