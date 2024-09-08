<?php

namespace App\Service;

use App\Interface\BinFetcherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use JMS\Serializer\SerializerInterface;

readonly class BinFetcher implements BinFetcherInterface
{

    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        #[Autowire('%bin_endpoint.url%')]
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
    public function fetchBinData(string $bin): string
    {
        $response = $this->httpClient->request('GET', $this->binUrl . $bin);
        $cardInfoData = $this->serializer->deserialize(
            $response->getContent(),
            'array<App\DTO\CardInfoDTO>',
            'json'
        );

        return $cardInfoData->getCountry()->getAlpha2();
    }
}
