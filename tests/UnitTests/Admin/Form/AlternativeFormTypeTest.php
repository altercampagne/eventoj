<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Admin\Form;

use App\Admin\Form\AlternativeFormType;
use App\Entity\Alternative;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class AlternativeFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    private FormInterface $form;

    protected function setUp(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(AlternativeFormType::class, new Alternative(), [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $this->form->submit([
            'name' => 'Nouvelle alternative',
            'website' => 'https://inscrpitions.altercampagne.net',
            'description' => 'Une superbe alternative',
            'address' => [
                'countryCode' => 'FR',
                'addressLine1' => $faker->address(),
                'zipCode' => $faker->postCode(),
                'city' => $faker->city(),
            ],
            'stations' => [
                [
                    'type' => 'train',
                    'name' => 'A train station',
                    'distance' => 42,
                ],
            ],
        ]);
        $this->assertFormValid($this->form);
    }
}
