<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Alternative;
use App\Message\GeocodeAlternativeAddressMessage;
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
    name: 'eventoj:geocode:alternatives',
    description: 'Geocode the address associated to alternatives.',
)]
class GeocodeAlternativeCommand
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
        #[Argument(description: 'Geocode only this given alternative.')]
        ?string $slug = null,
        #[Option(description: 'Do geocoding in async or not. Always false for 1 alternative, default true for all alternatives.')]
        bool $async = true,
        #[Option(description: 'Force geocoding, even if coordinates are already filled.')]
        bool $force = false,
    ): int {
        $io = new SymfonyStyle($input, $output);
        if (null !== $slug) {
            if (null === $alternative = $this->em->getRepository(Alternative::class)->findOneBySlug($slug)) {
                throw new \InvalidArgumentException("Alternative with slug {$slug} not found!");
            }

            if ($alternative->getAddress()->isGeocoded() && !$force) {
                $io->warning('Alternative address is already geocoded, ignoring (use --force to geocode it anyway).');

                return Command::SUCCESS;
            }

            if ($this->addressGeocoder->geocode($alternative)) {
                $io->success('Alternative address have been successfully geocoded.');
            } else {
                $io->warning('Alternative address not found by geocoder! :/');
            }

            return Command::SUCCESS;
        }

        $alternatives = $this->em->getRepository(Alternative::class)->findAll();
        foreach ($alternatives as $alternative) {
            if ($alternative->getAddress()->isGeocoded() && !$force) {
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
