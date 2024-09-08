<?php

namespace App\Tests\Service;

use App\Service\ExchangeRateFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRateFetcherTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private ExchangeRateFetcher $exchangeRateFetcher;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->exchangeRateFetcher = new ExchangeRateFetcher($this->httpClient, 'https://api.example.com/exchange-rates');
    }

    /**
     * @dataProvider currencyDataProvider
     */
    public function testFetchExchangeRate(string $currency, float $expectedRate)
    {
        // Mock the HTTP response
        $response = $this->createMock(ResponseInterface::class);
        $currencyData = file_get_contents(__DIR__ . '/currency/currencyData.json');
        if ($currencyData === false) {
            throw new \RuntimeException('Failed to read currencyData.json');
        }
        $response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn(json_decode($currencyData, true));

        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.example.com/exchange-rates')
            ->willReturn($response);

        $rate = $this->exchangeRateFetcher->fetchExchangeRate($currency);

        $this->assertEquals($expectedRate, $rate);
    }

    public static function currencyDataProvider(): array
    {
        $inputFile = __DIR__ . '/currency/inputData.txt';
        if (!file_exists($inputFile)) {
            throw new \RuntimeException("Input file not found: $inputFile");
        }

        $inputData = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $currencies = [];

        foreach ($inputData as $line) {
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['country'])) {
                throw new \RuntimeException('Invalid JSON or missing "country" key in input file');
            }

            $currency = $data['country'];
            $expectedRate = self::getExpectedRate($currency);
            $currencies[] = [$currency, $expectedRate];
        }

        return $currencies;
    }

    private static function getExpectedRate(string $currency): float
    {
        $currencyDataFile = __DIR__ . '/currency/currencyData.json';
        if (!file_exists($currencyDataFile)) {
            throw new \RuntimeException("Currency data file not found: $currencyDataFile");
        }

        $data = file_get_contents($currencyDataFile);
        if ($data === false) {
            throw new \RuntimeException("Failed to read currency data file: $currencyDataFile");
        }

        $currencyData = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in currency data file');
        }

        return $currencyData['rates'][$currency] ?? 0.0;
    }
}
