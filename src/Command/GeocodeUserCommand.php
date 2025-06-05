<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Message\GeocodeUserAddressMessage;
use App\Service\AddressGeocoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'eventoj:geocode:users',
    description: 'Geocode the address associated to users.',
)]
class GeocodeUserCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
        private readonly AddressGeocoder $addressGeocoder,
    ) {
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Argument(description: 'Geocode only this given user.')]
        ?string $email = null,
        #[Option(description: 'Do geocoding in async or not. Always false for 1 user, default true for all users.')]
        bool $async = true,
        #[Option(description: 'Force geocoding, even if coordinates are already filled.')]
        bool $force = false,
    ): int {
        $io = new SymfonyStyle($input, $output);
        if (null !== $email) {
            if (null === $user = $this->em->getRepository(User::class)->findOneByEmail($email)) {
                throw new \InvalidArgumentException("User with email {$email} not found!");
            }

            if ($user->getAddress()->isGeocoded() && !$force) {
                $io->warning('User address is already geocoded, ignoring (use --force to geocode it anyway).');

                return Command::SUCCESS;
            }

            if ($this->addressGeocoder->geocode($user)) {
                $io->success('User address have been successfully geocoded.');
            } else {
                $io->warning('User address not found by geocoder! :/');
            }

            return Command::SUCCESS;
        }

        $users = $this->em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            if ($user->getAddress()->isGeocoded() && !$force) {
                $output->writeln("User {$user->getEmail()} address is already geocoded, ignoring", OutputInterface::VERBOSITY_VERBOSE);

                continue;
            }

            if ($input->hasParameterOption(['--no-async'], true)) {
                $this->addressGeocoder->geocode($user);

                $output->writeln("User {$user->getEmail()} have been geocoded.", OutputInterface::VERBOSITY_VERBOSE);
            } else {
                $this->bus->dispatch(new GeocodeUserAddressMessage($user->getId()));

                $output->writeln("User {$user->getEmail()} will be geocoded.", OutputInterface::VERBOSITY_VERBOSE);
            }
        }

        $io->success('Users addresses geocoded!.');

        return Command::SUCCESS;
    }
}
