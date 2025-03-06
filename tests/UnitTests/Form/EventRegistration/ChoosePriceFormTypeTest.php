<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\Form\EventRegistration\ChoosePriceFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChoosePriceFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    /** @var FormInterface<array{price: int}> */
    private FormInterface $form;

    protected function setUp(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ChoosePriceFormType::class, ['price' => 35000], [
            'csrf_protection' => false,
            'minimum_price' => 25000,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            'price' => 350,
            'acceptCharter' => true,
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testErrors(): void
    {
        $this->form->submit([
            'price' => 100,
            'acceptCharter' => false,
        ]);

        $this->assertFormInvalid($this->form, [
            'price' => "Le prix minimum est de 250\u{a0}€.",
            'acceptCharter' => "L'acceptation de la charte n'est pas optionnelle !",
        ]);
    }
}
