<?php

namespace App\Service;

use App\DTO\CardInfoDTO;
use App\Interface\BinFetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class BinFetcher implements BinFetcherInterface
{

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%bin_endpoint%')]
        private string $binUrl
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchCountryCode(string $bin): ?string
    {
        $response = $this->httpClient->request('GET', $this->binUrl . $bin);
        $content = json_decode($response->getContent(), true);

        return $content['country']['alpha2'] ?? null;
    }

}
