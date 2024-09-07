<?php

namespace App\Command;

use App\Model\Transaction;
use App\Service\TransactionParser;
use App\Service\TransactionProcessor;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;

class ProcessTransactionsCommand extends Command
{
    protected static string $defaultName = 'app:process-transactions';

    public function __construct(
        private readonly TransactionProcessor $transactionProcessor,
        private readonly TransactionParser $transactionParser
    )
    {
        parent::__construct();;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Processes transactions from a JSON file.')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to the JSON file with transactions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');

        try {
            $transactions = $this->transactionParser->readTransactions($filePath);
            $fees = $this->transactionProcessor->processTransactions($transactions);
            $this->showFees($fees, $output);

        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function showFees(array $fees, OutputInterface $output): void
    {
        foreach ($fees as $fee) {
            $output->writeln('Fee: ' . $fee);
        }
    }
}
