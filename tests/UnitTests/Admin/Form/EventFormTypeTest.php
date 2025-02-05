<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Admin\Form;

use App\Admin\Form\EventFormType;
use App\Entity\Event;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class EventFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    /** @var FormInterface<Event> */
    private FormInterface $form;

    protected function setUp(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(EventFormType::class, Event::AT(), [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            'name' => 'AT 2042',
            'description' => 'Un AT qui s\'annonce incoyable',
            'openingDateForBookings' => (new \DateTimeImmutable('3 months'))->format('Y-m-d H:i:s'),
            'firstMealOfFirstDay' => 'dinner',
            'lastMealOfLastDay' => 'breakfast',
            'adultsCapacity' => 30,
            'childrenCapacity' => 6,
            'bikesAvailable' => 6,
            'minimumPricePerDay' => 20,
            'breakEvenPricePerDay' => 33,
            'supportPricePerDay' => 47,
            'daysAtSolidarityPrice' => 8,
        ]);
        $this->assertFormValid($this->form);
    }
}
