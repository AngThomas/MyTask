<?php

namespace App\Service;

use App\Model\Transaction;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

readonly class TransactionParser
{
    public function __construct(
        private SerializerInterface $serializer,
        private Filesystem $filesystem
    )
    {
    }

    /**
     * @return Transaction[]
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function readTransactions(string $filePath): array
    {
        $fileContent = $this->readFile($filePath);
        return $this->deserializeTransactions($fileContent);
    }

    private function readFile(string $filePath): string
    {
        if (!$this->filesystem->exists($filePath)) {
            throw new FileNotFoundException('File does not exist.');
        }

        return file_get_contents($filePath);

    }

    /**
     * @param string $fileContent
     * @return Transaction[]
     */
    private function deserializeTransactions(string $fileContent): array
    {
        return $this->serializer->deserialize(
            $fileContent,
            'array<App\Model\Transaction>',
            'json'
        );
    }
}
