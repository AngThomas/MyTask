<?php

namespace App\Interface;

interface ExchangeRateFetcherInterface
{
    public function fetchExchangeRate(string $currency): float;
}