<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\Entity\Event;
use App\Entity\Meal;
use App\Form\EventRegistration\ChooseDatesFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\UnitTests\FormAssertionsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChooseDatesFormTypeTest extends KernelTestCase
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
        $this->form = $formFactory->create(ChooseDatesFormType::class, new EventRegistrationDTO($event), [
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
}
