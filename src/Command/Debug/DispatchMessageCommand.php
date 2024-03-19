<?php

declare(strict_types=1);

namespace App\Command\Debug;

use App\Entity\Registration;
use App\Entity\RegistrationStatus;
use App\Message\PahekoRegistrationSync;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'eventoj:debug:dispatch-message',
    description: 'This command is useful to dispatch messages for debug purpose.',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class DispatchMessageCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::OPTIONAL, 'The message to dispatch. If null, all messages will be dispatched.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ?string $message */
        $message = $input->getArgument('message');

        $io = new SymfonyStyle($input, $output);

        match ($message) {
            'paheko_registration_sync' => $this->dispatchPahekoRegistrationSync(),
            null => $this->dispatchAllMessages(),
            default => throw new \InvalidArgumentException("Unknown message \"$message\"."),
        };

        $io->success(null !== $message ? "Message  \"$message\" dispatched!" : 'Messages dispatched!');

        return Command::SUCCESS;
    }

    private function dispatchAllMessages(): void
    {
        $this->dispatchPahekoRegistrationSync();
    }

    private function dispatchPahekoRegistrationSync(): void
    {
        $registration = $this->em->getRepository(Registration::class)->findBy(['status' => RegistrationStatus::CONFIRMED])[0];

        $this->bus->dispatch(new PahekoRegistrationSync($registration->getId()));
    }
}
