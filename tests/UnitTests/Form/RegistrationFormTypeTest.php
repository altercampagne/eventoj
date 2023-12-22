<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class RegistrationFormTypeTest extends KernelTestCase
{
    public function testSubmitValidData(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $formData = [
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'birthDate' => $faker->date(),
            'email' => $faker->email(),
            'phoneNumber' => $faker->phoneNumber(),
            'address' => [
                'countryCode' => 'FR',
                'addressLine1' => $faker->address(),
                'zipCode' => $faker->postCode(),
                'city' => $faker->city(),
            ],
            'plainPassword' => 'password',
        ];

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
    }

    public function testSomeErrors(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $formData = [
            'email' => 'admin@altercampagne.net',
            'phoneNumber' => '0101010101',
            'birthDate' => $faker->date(),
            'address' => [
                'countryCode' => 'FR',
            ],
            'plainPassword' => 'bla',
        ];

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()?->getName()] = $error->getMessage();
        }

        $this->assertSame([
            'email' => 'Il y a déjà un compte avec cette adresse mail',
            'firstName' => 'Cette valeur ne doit pas être vide.',
            'lastName' => 'Cette valeur ne doit pas être vide.',
            'plainPassword' => 'Ton mot de passe doit faire au moins 7 caractères.',
        ], $errors);
    }
}
