<?php

namespace App\Service;

use App\Interface\ExchangeRateFetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class ExchangeRateFetcher implements ExchangeRateFetcherInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%currency_endpoint.url%')]
        private string $exchangeRateUrl
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     */
    public function fetchExchangeRate(string $currency): float
    {
        $response = $this->httpClient->request('GET', $this->exchangeRateUrl);
        $rates = $response->toArray()['rates'];
        return $rates[$currency] ?? 0.0;
    }
}
