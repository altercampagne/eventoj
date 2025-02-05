<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\Entity\Event;
use App\Entity\Meal;
use App\Entity\Registration;
use App\Entity\User;
use App\Factory\CompanionFactory;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use App\Form\EventRegistration\ChooseDatesFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\UnitTests\FormAssertionsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Zenstruck\Foundry\Test\Factories;

class ChooseDatesFormTypeTest extends KernelTestCase
{
    use Factories;
    use FormAssertionsTrait;

    private Event $event;
    private User $user;
    private Registration $registration;
    /** @var FormInterface<EventRegistrationDTO> */
    private FormInterface $form;

    protected function setUp(): void
    {
        $this->user = UserFactory::createOne()->_real();
        $this->event = EventFactory::new()->published()->withRandomStages()->create()->_real();

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

    public function testWithTooManyChildren(): void
    {
        $companions = [];
        for ($i = 0; $i < $this->event->getAdultsCapacity() + 2; ++$i) {
            $companions[] = CompanionFactory::new()->children()->create(['user' => $this->user])->_real();
        }
        $this->registration->setCompanions(new ArrayCollection($companions));

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
        for ($i = 0; $i < $this->event->getAdultsCapacity() + 2; ++$i) {
            $companions[] = CompanionFactory::new()->adult()->create(['user' => $this->user])->_real();
        }
        $this->registration->setCompanions(new ArrayCollection($companions));

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
