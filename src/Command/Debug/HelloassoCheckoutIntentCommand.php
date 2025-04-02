<?php

declare(strict_types=1);

namespace App\Command\Debug;

use Helloasso\HelloassoClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'eventoj:debug:helloasso:checkout-intent',
    description: 'Show CheckoutIntent details retrieved from helloasso',
)]
class HelloassoCheckoutIntentCommand extends Command
{
    public function __construct(
        private readonly HelloassoClient $helloassoClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'ID du CheckoutIntent Helloasso à afficher')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $id */
        $id = $input->getArgument('id');

        dump($this->helloassoClient->checkout->retrieve((int) $id));

        return Command::SUCCESS;
    }
}
