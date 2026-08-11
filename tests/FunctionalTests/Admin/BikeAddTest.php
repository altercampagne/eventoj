<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Entity\Registration;
use App\Entity\StageRegistration;
use App\Factory\EventFactory;
use App\Factory\RegistrationFactory;
use App\Factory\StageFactory;
use App\Factory\UserFactory;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class BikeAddTest extends WebTestCase
{
    use Factories;

    public function testCannotAddBikeWhenMoreBikesThanPeople(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->create(['bikesAvailable' => 5]);

        $registration = RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->create(['event' => $event, 'neededBike' => 1]);

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/bike_add");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-danger', 'Impossible de demander plus de vélos que de personnes !');

        /** @var RegistrationRepository $registrationRepository */
        $registrationRepository = $this->getContainer()->get(RegistrationRepository::class);
        $refreshed = $registrationRepository->find($registration->getId());
        $this->assertNotNull($refreshed);
        $this->assertSame(1, $refreshed->getNeededBike());
    }

    public function testAddBikeSucceedsWhenBikesAreAvailable(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->create(['bikesAvailable' => 5]);

        $registration = RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->create(['event' => $event, 'neededBike' => 0]);

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/bike_add");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', "Un vélo a été ajouté à l'inscription.");

        /** @var RegistrationRepository $registrationRepository */
        $registrationRepository = $this->getContainer()->get(RegistrationRepository::class);
        $refreshed = $registrationRepository->find($registration->getId());
        $this->assertNotNull($refreshed);
        $this->assertSame(1, $refreshed->getNeededBike());
    }

    public function testCannotAddBikeWhenNoBikesAvailableForAnIncludedMeal(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->create(['bikesAvailable' => 1]);
        StageFactory::new()->create(['event' => $event]);

        // Another confirmed registration consumes the only bike available for
        // every meal of this stage.
        RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->afterInstantiate(static function (Registration $registration): void {
                $registration->confirm();
            })
            ->create(['event' => $event, 'neededBike' => 1]);

        // Present for breakfast (default), which is exactly the meal for
        // which bikes are exhausted.
        $registration = RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->create(['event' => $event, 'neededBike' => 0]);

        $stageRegistration = $registration->getStagesRegistrations()->first();
        $this->assertInstanceOf(StageRegistration::class, $stageRegistration);
        $confirmedStageRegistrations = $stageRegistration->getStage()->getConfirmedStagesRegistrations();
        $this->assertCount(1, $confirmedStageRegistrations);
        /** @var StageRegistration $confirmedStageRegistration */
        $confirmedStageRegistration = $confirmedStageRegistrations->first();
        $this->assertTrue($confirmedStageRegistration->getRegistration()->isConfirmed());
        $this->assertSame(1, $confirmedStageRegistration->getRegistration()->getNeededBike());

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/bike_add");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-danger', 'Plus de vélo disponible sur cette période.');

        /** @var RegistrationRepository $registrationRepository */
        $registrationRepository = $this->getContainer()->get(RegistrationRepository::class);
        $refreshed = $registrationRepository->find($registration->getId());
        $this->assertNotNull($refreshed);
        $this->assertSame(0, $refreshed->getNeededBike());
    }

    /**
     * Regression test: bikes availability of a meal must be ignored when the
     * stage registration does not include that meal (see BikeAddController).
     */
    public function testAddBikeSucceedsWhenNoBikesAvailableOnlyForAnExcludedMeal(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->AT()->create(['bikesAvailable' => 1]);
        StageFactory::new()->create(['event' => $event]);

        // Another confirmed registration consumes the only bike available,
        // but only for breakfast.
        RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->afterInstantiate(static function (Registration $registration): void {
                /** @var StageRegistration $stageRegistration */
                $stageRegistration = $registration->getStagesRegistrations()->first();
                $stageRegistration->setPresentForLunch(false);
                $stageRegistration->setPresentForDinner(false);

                $registration->confirm();
            })
            ->create(['event' => $event, 'neededBike' => 1]);

        // This registration does not include breakfast, the only meal with
        // no bike availability left. Lunch and dinner still have bikes free.
        $registration = RegistrationFactory::new()
            ->withStagesRegistrations(0, 1)
            ->afterInstantiate(static function (Registration $registration): void {
                /** @var StageRegistration $stageRegistration */
                $stageRegistration = $registration->getStagesRegistrations()->first();
                $stageRegistration->setPresentForBreakfast(false);
            })
            ->create(['event' => $event, 'neededBike' => 0]);

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/bike_add");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', "Un vélo a été ajouté à l'inscription.");

        /** @var RegistrationRepository $registrationRepository */
        $registrationRepository = $this->getContainer()->get(RegistrationRepository::class);
        $refreshed = $registrationRepository->find($registration->getId());
        $this->assertNotNull($refreshed);
        $this->assertSame(1, $refreshed->getNeededBike());
    }
}
