<?php

declare(strict_types=1);

namespace App\Command\Debug;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'eventoj:debug:event:clean',
    description: 'Clean an event by removing everything linked to it (registrations, payments, memberships, ...)',
)]
class EventCleanCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRepository $eventRepository,
        #[Autowire(param: 'kernel.environment')]
        private readonly string $environment,
        #[Autowire(env: 'bool:STAGING')]
        private readonly bool $staging)
    {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument("Slug de l'évènement à cleaner.")]
        string $slug,
    ): int {
        $io = new SymfonyStyle($input, $output);
        if ('prod' === $this->environment && !$this->staging) {
            $io->error('Impossible de lancer cette commande en prod !');

            return Command::FAILURE;
        }

        if (!$io->confirm("Cette action est totalement irréversible, es-tu sûr⋅e de vouloir supprimer toutes les données liées à l'évènement {$slug}.", false)) {
            return Command::SUCCESS;
        }

        if (null === $event = $this->eventRepository->findOneBySlugJoinedToAllChildEntities($slug)) {
            $io->error("L'évènement \"{$slug}\" n'a pas été trouvé.");

            return Command::FAILURE;
        }

        foreach ($event->getRegistrations() as $registration) {
            foreach ($registration->getPayments() as $payment) {
                foreach ($payment->getMemberships() as $membership) {
                    $this->em->remove($membership);
                }

                $this->em->remove($payment);
            }

            $this->em->remove($registration);
        }

        $this->em->flush();
        $io->success('Toutes les inscriptions / paiements / adhésions liés à cet évènement ont été supprimés.');

        return Command::SUCCESS;
    }
}
