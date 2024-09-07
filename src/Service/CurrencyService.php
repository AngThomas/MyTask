<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CurrencyService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%currency_endpoint.url%')]
        private readonly string $exchangeRateUrl
    )
    {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     */
    public function getRate(string $currency): float
    {
        $response = $this->httpClient->request('GET', $this->exchangeRateUrl);
        $rates = $response->toArray()['rates'];
        return $rates[$currency] ?? 0.0;
    }

    /**
     * @throws \Exception
     */
    public function convertToEur(float $amount, string $currency): float
    {
        if ($currency === 'EUR') {
            return $amount;
        }

        $rate = $this->getRate($currency);
        if ($rate == 0) {
            throw new \Exception("Currency rate for {$currency} not found");
        }

        return $amount / $rate;
    }
}
