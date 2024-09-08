<?php

namespace App\Command;

use App\Service\TransactionParser;
use App\Service\TransactionProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ProcessTransactions',
    description: 'Sends the newsletter to new active users.',
)]
class ProcessTransactionsCommand extends Command
{
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
            $commissions = $this->transactionProcessor->processTransactions($transactions);
            $this->showCommissions($commissions, $output);

        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function showCommissions(array $commissions, OutputInterface $output): void
    {
        foreach ($commissions as $commission) {
            $output->writeln('Commission: ' . $commission);
        }
    }
}
