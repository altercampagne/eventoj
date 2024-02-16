<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Registration;
use App\Form\EventRegistration\ChoosePriceFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\DatabaseUtilTrait;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChoosePriceFormTypeTest extends KernelTestCase
{
    use DatabaseUtilTrait;
    use FormAssertionsTrait;

    private FormInterface $form;

    protected function setUp(): void
    {
        $user = FixtureBuilder::createUser();
        $event = FixtureBuilder::createAT();
        $registration = new Registration($user, $event);
        $this->save($user, $event, $registration, FixtureBuilder::createCompanion(user: $user, children: false), FixtureBuilder::createCompanion(user: $user, children: true));

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ChoosePriceFormType::class, new EventRegistrationDTO($registration), [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            'pricePerDay' => 35,
            'acceptCharter' => true,
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testErrors(): void
    {
        $this->form->submit([
            'pricePerDay' => 10,
            'acceptCharter' => false,
        ]);

        $this->assertFormInvalid($this->form, [
            'pricePerDay' => 'Le prix minimum par jour est de 20 €.',
            'acceptCharter' => 'L\'acceptation de la charte n\'est pas optionnelle !',
        ]);
    }
}
