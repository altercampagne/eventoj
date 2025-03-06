<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Message\GeocodeUserAddressMessage;
use App\Service\AddressGeocoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'eventoj:geocode:users',
    description: 'Geocode the address associated to users.',
)]
class GeocodeUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
        private readonly AddressGeocoder $addressGeocoder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Geocode only this given user.')
            ->addOption('async', null, InputOption::VALUE_NEGATABLE, 'Do geocoding in async or not. Default false for 1 user, true for all users.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force geocoding, even if coordinates are already filled.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $force = (bool) $input->getOption('force');

        if (null !== $email = $input->getArgument('email')) {
            if (null === $user = $this->em->getRepository(User::class)->findOneByEmail($email)) {
                /* @phpstan-ignore-next-line */
                throw new \InvalidArgumentException("User with email {$email} not found!");
            }

            if ($user->getAddress()->isGeocoded() && !$force) {
                $io->warning('User address is already geocoded, ignoring (use --force to geocode it anyway).');

                return Command::SUCCESS;
            }

            if ($input->hasParameterOption(['--async'], true)) {
                $this->bus->dispatch(new GeocodeUserAddressMessage($user->getId()));
                $io->success('User address will be geocoded.');
            } elseif ($this->addressGeocoder->geocode($user)) {
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
