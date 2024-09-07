<?php

namespace App\Service;

use App\Enum\EUCountry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
readonly class BinService
{

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%bin_endpoint.url%')]
        private string $binUrl
    )
    {
    }

    public function getCountryCode(string $bin): string
    {
        $response = $this->httpClient->request('GET', $this->binUrl . $bin);
        $binData = $response->toArray();
        return $binData['country']['alpha2'] ?? '';
    }

    public function isEuropeanCountry(string $countryCode): bool
    {
        return EUCountry::isEU($countryCode);
    }
}
