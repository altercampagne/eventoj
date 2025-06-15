<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Payment;
use App\Service\Helloasso\PaymentSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'eventoj:helloasso:sync:payments',
    description: 'Sync payments from the Database with Helloasso',
    help: <<<TXT
        The <info>%command.name%</info> command sync payments from the database
        with Helloasso. This is useful in various situation:

        In a cron, to move pending payments to expired & ensure all payments
        are in sync with Helloasso. It's very useful if the callback have not
        been called when a payment have been successfully made on Helloasso.

        <info>php %command.full_name% --from "-1hour"</info>

        In september, in order to make one big sync of all payments made during
        the summer:

        <info>php %command.full_name% --from "-6months"</info>

        It's also possible to sync only 1 payment using the <info>--id</info>
        option:

        <info>php %command.full_name% --id [an_uuid]</info>

        TXT
)]
class HelloassoSyncPaymentsCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PaymentSynchronizer $paymentSynchronizer,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option("The id of the payment to sync. If null, all matching payments will be sync'ed.")]
        ?string $id = null,
        #[Option('Sync only payments created AFTER the calculated date (not relevant if an ID is provided).')]
        ?string $from = null,
    ): int {
        $io = new SymfonyStyle($input, $output);
        if (null !== $id) {
            if (null === $payment = $this->em->getRepository(Payment::class)->findOneById($id)) {
                throw new \InvalidArgumentException('Payment not found!');
            }

            $this->paymentSynchronizer->sync($payment);

            $io->success("Payment have been sync'ed on Helloasso!");

            return Command::SUCCESS;
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p')
            ->from(Payment::class, 'p')
            ->orderBy('p.createdAt', 'ASC')
        ;
        if (null !== $from) {
            $qb
                ->andWhere('p.createdAt >= :from')
                ->setParameter('from', new \DateTimeImmutable($from))
            ;
        }

        $payments = iterator_to_array($qb->getQuery()->toIterable());

        $io->progressStart(\count($payments));
        foreach ($payments as $payment) {
            try {
                $syncReport = $this->paymentSynchronizer->sync($payment);
            } catch (\Exception $e) {
                $io->error("An error occurred when syncing payment {$payment->getId()} with Helloasso : {$e->getMessage()}.");
            }

            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success("All payments have been sync'ed on Helloasso!");

        return Command::SUCCESS;
    }
}
