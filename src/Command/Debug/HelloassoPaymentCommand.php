<?php

declare(strict_types=1);

namespace App\Command\Debug;

use Helloasso\HelloassoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'eventoj:debug:helloasso:payment',
    description: 'Show Payment details retrieved from helloasso',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class HelloassoPaymentCommand extends Command
{
    public function __construct(
        private readonly HelloassoClient $helloassoClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'ID du Payment Helloasso à afficher')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $id */
        $id = $input->getArgument('id');

        dump($this->helloassoClient->payment->retrieve((int) $id));

        return Command::SUCCESS;
    }
}
