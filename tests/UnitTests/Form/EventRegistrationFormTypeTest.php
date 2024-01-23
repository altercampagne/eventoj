<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Entity\Event;
use App\Entity\Meal;
use App\Form\EventRegistrationDTO;
use App\Form\EventRegistrationFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class EventRegistrationFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    private Event $event;
    private FormInterface $form;

    protected function setUp(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get(EntityManagerInterface::class);

        $event = $em->getRepository(Event::class)->findOneBy([
            'slug' => 'at-a-venir-ouvert',
        ]);

        if (null === $event) {
            throw new \RuntimeException('Unable to find test event!');
        }

        $this->event = $event;

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(EventRegistrationFormType::class, new EventRegistrationDTO($event), [
            'csrf_protection' => false,
            'event' => $this->event,
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
            'needBike' => false,
            'pricePerDay' => 33,
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithInvalidDates(): void
    {
        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()->last()->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()->first()->getId(),
            'lastMeal' => Meal::LUNCH->value,
            'needBike' => false,
            'pricePerDay' => 0,
        ]);

        $this->assertFormInvalid($this->form, [
            'event_registration_form' => 'Tu ne peux pas repartir avant même d\'être arrivé.',
            'pricePerDay' => 'Le prix minimum par jour est de 20 €.',
        ]);
    }

    public function testWithTooShortPeriod(): void
    {
        $this->form->submit([
            /* @phpstan-ignore-next-line */
            'stageStart' => (string) $this->event->getStages()[0]->getId(),
            'firstMeal' => Meal::LUNCH->value,
            /* @phpstan-ignore-next-line */
            'stageEnd' => (string) $this->event->getStages()[1]->getId(),
            'lastMeal' => Meal::LUNCH->value,
            'needBike' => false,
            'pricePerDay' => 33,
        ]);

        $this->assertFormInvalid($this->form, [
            'event_registration_form' => 'Tu dois rester au minimum 4 jours sur l\'évènement.',
        ]);
    }
}
