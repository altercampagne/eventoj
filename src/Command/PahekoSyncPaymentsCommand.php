<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Payment;
use App\Service\Paheko\PaymentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:paheko:sync:payments',
    description: 'Sync payments from the Database with Paheko',
)]
class PahekoSyncPaymentsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PaymentSynchronizer $paymentSynchronizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::OPTIONAL, "The id of the payment to sync. If null, all payments will be sync'ed.")
        ;
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (null !== $id = $input->getArgument('id')) {
            if (null === $payment = $this->em->getRepository(Payment::class)->findOneById($id)) {
                throw new \InvalidArgumentException('Payment not found!');
            }

            $this->paymentSynchronizer->sync($payment);

            $io->success("Payment have been sync'ed on Paheko!");

            return Command::SUCCESS;
        }

        $payments = $this->em->getRepository(Payment::class)->findAll();

        $io->progressStart(\count($payments));

        foreach ($payments as $payment) {
            $this->paymentSynchronizer->sync($payment);
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success("All payments have been sync'ed on Paheko!");

        return Command::SUCCESS;
    }
}
