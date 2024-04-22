<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Alternative;
use App\Message\GeocodeAlternativeAddressMessage;
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
    name: 'eventoj:geocode:alternatives',
    description: 'Geocode the address associated to alternatives.',
)]
class GeocodeAlternativeCommand extends Command
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
            ->addArgument('slug', InputArgument::OPTIONAL, 'Geocode only this given alternative.')
            ->addOption('async', null, InputOption::VALUE_NEGATABLE, 'Do geocoding in async or not. Default false for 1 alternative, true for all alternatives.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force geocoding, even if coordinates are already filled.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $force = (bool) $input->getOption('force');

        if (null !== $slug = $input->getArgument('slug')) {
            if (null === $alternative = $this->em->getRepository(Alternative::class)->findOneBySlug($slug)) {
                /* @phpstan-ignore-next-line */
                throw new \InvalidArgumentException("Alternative with slug $slug not found!");
            }

            if (null !== $alternative->getAddress()->getLatitude() && !$force) {
                $io->warning('Alternative address is already geocoded, ignoring (use --force to geocode it anyway).');

                return Command::SUCCESS;
            }

            if ($input->hasParameterOption(['--async'], true)) {
                $this->bus->dispatch(new GeocodeAlternativeAddressMessage($alternative->getId()));

                $io->success('Alternative address will be geocoded.');
            } else {
                if ($this->addressGeocoder->geocode($alternative)) {
                    $io->success('Alternative address have been successfully geocoded.');
                } else {
                    $io->warning('Alternative address not found by geocoder! :/');
                }
            }

            return Command::SUCCESS;
        }

        $alternatives = $this->em->getRepository(Alternative::class)->findAll();
        foreach ($alternatives as $alternative) {
            if (null !== $alternative->getAddress()->getLatitude() && !$force) {
                $output->writeln("Alternative {$alternative->getName()} address is already geocoded, ignoring", OutputInterface::VERBOSITY_VERBOSE);

                continue;
            }

            if ($input->hasParameterOption(['--no-async'], true)) {
                $this->addressGeocoder->geocode($alternative);

                $output->writeln("Alternative {$alternative->getName()} have been geocoded.", OutputInterface::VERBOSITY_VERBOSE);
            } else {
                $this->bus->dispatch(new GeocodeAlternativeAddressMessage($alternative->getId()));

                $output->writeln("Alternative {$alternative->getName()} will be geocoded.", OutputInterface::VERBOSITY_VERBOSE);
            }
        }

        $io->success('Alternatives addresses geocoded!.');

        return Command::SUCCESS;
    }
}
