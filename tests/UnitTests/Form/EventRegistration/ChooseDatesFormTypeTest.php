<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChooseDatesFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\DatabaseUtilTrait;
use App\Tests\UnitTests\FormAssertionsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChooseDatesFormTypeTest extends KernelTestCase
{
    use DatabaseUtilTrait;
    use FormAssertionsTrait;

    private Event $event;
    private User $user;
    private Registration $registration;
    private FormInterface $form;

    protected function setUp(): void
    {
        $this->user = FixtureBuilder::createUser();
        $this->event = FixtureBuilder::createAT();
        $this->save($this->user, $this->event);

        $this->registration = new Registration($this->user, $this->event);

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ChooseDatesFormType::class, new EventRegistrationDTO($this->registration), [
            'csrf_protection' => false,
            'registration' => $this->registration,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()->first()->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()->last()->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithInvalidDates(): void
    {
        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[10]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[5]->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);

        $this->assertFormInvalid($this->form, [
            'choose_dates_form' => 'Tu ne peux pas repartir avant même d\'être arrivé.',
        ]);
    }

    public function testWithTooShortPeriod(): void
    {
        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[3]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[3]->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);

        $this->assertFormInvalid($this->form, [
            'choose_dates_form' => 'Tu dois rester au minimum une journée sur l\'évènement.',
        ]);
    }

    public function testWithTooManyChildren(): void
    {
        $companions = [];
        $ids = [];
        for ($i = 0; $i < $this->event->getChildrenCapacity() + 2; ++$i) {
            $companion = FixtureBuilder::createCompanion(user: $this->user, children: true);
            $ids[] = (string) $companion->getId();
            $companions[] = $companion;
        }
        $this->registration->setCompanions(new ArrayCollection($companions));
        $this->save($this->registration, ...$companions);

        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[0]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[6]->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);

        $this->assertFormInvalid($this->form, [
            'choose_dates_form' => 'Il n\'y a pas assez de disponibilités sur la période sélectionnée.',
        ]);
    }

    public function testWithTooManyAdults(): void
    {
        $companions = [];
        $ids = [];
        for ($i = 0; $i < $this->event->getAdultsCapacity() + 2; ++$i) {
            $companion = FixtureBuilder::createCompanion(user: $this->user, children: false);
            $ids[] = (string) $companion->getId();
            $companions[] = $companion;
        }
        $this->registration->setCompanions(new ArrayCollection($companions));
        $this->save($this->registration, ...$companions);

        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[0]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[6]->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);

        $this->assertFormInvalid($this->form, [
            'choose_dates_form' => 'Il n\'y a pas assez de disponibilités sur la période sélectionnée.',
        ]);
    }

    public function testWithTooManyBikes(): void
    {
        $this->registration->setNeededBike($this->event->getBikesAvailable() + 1);
        $this->save($this->registration);

        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[0]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[6]->getId(),
            'lastMeal' => Meal::LUNCH->value,
        ]);

        $this->assertFormInvalid($this->form, [
            'choose_dates_form' => 'Il n\'y a pas assez de disponibilités sur la période sélectionnée.',
        ]);
    }
}
