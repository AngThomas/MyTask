<?php

use App\Service\BinFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BinFetcherTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private BinFetcher $binFetcher;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->binFetcher = new BinFetcher($this->httpClient, 'https://bin-check.com/');
    }

    /**
     * @dataProvider binProvider
     */
    public function testFetchCountryCode(string $bin, ?string $expectedCountryCode)
    {
        // Create mock response
        $response = $this->createMock(ResponseInterface::class);
        $binData = $this->getBinDataForTest($bin);
        $response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($binData);

        // Configure the HTTP client mock to return the mock response
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://bin-check.com/' . $bin)
            ->willReturn($response);

        $result = $this->binFetcher->fetchCountryCode($bin);

        $this->assertEquals($expectedCountryCode, $result);
    }

    public static function binProvider(): array
    {
        $inputFile = __DIR__ . '/bin/inputData.txt';
        if (!file_exists($inputFile)) {
            throw new \RuntimeException("Input file not found: $inputFile");
        }

        $inputData = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $bins = [];

        foreach ($inputData as $line) {
            $data = json_decode($line, true);
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['bin'])) {
                throw new \RuntimeException('Invalid JSON or missing "bin" key in input file');
            }
            $bins[] = [$data['bin'], self::getExpectedCountryCode($data['bin'])];
        }

        return $bins;
    }

    private static function getExpectedCountryCode(string $bin): ?string
    {
        $binData = self::loadBinData();
        return $binData[$bin]['country']['alpha2'] ?? null;
    }

    private static function loadBinData(): array
    {
        $binDataFile = __DIR__ . '/bin/binData.json';
        if (!file_exists($binDataFile)) {
            throw new \RuntimeException("BIN data file not found: $binDataFile");
        }

        $data = file_get_contents($binDataFile);
        if ($data === false) {
            throw new \RuntimeException("Failed to read BIN data file: $binDataFile");
        }

        $binData = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON in BIN data file');
        }

        return $binData;
    }

    private function getBinDataForTest(string $bin): string
    {
        $binData = $this->loadBinData();

        // Check if the bin exists and return the corresponding JSON object
        if (isset($binData[$bin])) {
            return json_encode($binData[$bin]);
        }

        // Return an empty JSON object if the bin does not exist
        return json_encode([]);
    }
}
