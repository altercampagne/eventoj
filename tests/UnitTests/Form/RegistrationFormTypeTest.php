<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Form\RegistrationFormType;
use App\Tests\DatabaseUtilTrait;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class RegistrationFormTypeTest extends KernelTestCase
{
    use DatabaseUtilTrait;
    use FormAssertionsTrait;

    public function testSubmitValidData(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($this->getValidFormData());

        $this->assertFormValid($form);
    }

    public function testSomeErrors(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $formData = [
            'email' => $this->getRandomUser()->getEmail(), // Use a random user email to be sure this email exists
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

        $this->assertFormInvalid($form, [
            'email' => 'Il y a déjà un compte avec cette adresse mail',
            'firstName' => 'Cette valeur ne doit pas être vide.',
            'lastName' => 'Cette valeur ne doit pas être vide.',
            'plainPassword' => 'Ton mot de passe doit faire au moins 7 caractères.',
        ]);
    }

    /**
     * @dataProvider invalidNames
     */
    public function testInvalidNames(mixed $name, string $expectedError): void
    {
        $formData = $this->getValidFormData();
        $formData['firstName'] = $name;
        $formData['lastName'] = $name;

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($formData);

        $this->assertFormInvalid($form, [
            'firstName' => $expectedError,
            'lastName' => $expectedError,
        ]);
    }

    /**
     * @return iterable<array{?string, string}>
     */
    public static function invalidNames(): iterable
    {
        yield [null, 'Cette valeur ne doit pas être vide.'];
        yield ['', 'Cette valeur ne doit pas être vide.'];
        yield ['a', 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.'];
        yield ['Coucou3fail', 'Cette chaîne ne doit pas contenir de chiffres.'];
        yield ['COuUulala', 'Cette chaîne ne semble pas valide.'];
        yield ['PsDVll', 'Cette chaîne ne semble pas valide.'];
        yield ['abc foo', 'Cette chaîne contient des caractères spéciaux non autorisés.'];

        // Forbidden values
        yield ['firstname', 'Cette chaîne n\'est pas autorisée.'];
        yield ['lastname', 'Cette chaîne n\'est pas autorisée.'];
        yield ['unknown', 'Cette chaîne n\'est pas autorisée.'];
        yield ['first_name', 'Cette chaîne n\'est pas autorisée.'];
        yield ['last_name', 'Cette chaîne n\'est pas autorisée.'];
        yield ['anonyme', 'Cette chaîne n\'est pas autorisée.'];
        yield ['user', 'Cette chaîne n\'est pas autorisée.'];
        yield ['admin', 'Cette chaîne n\'est pas autorisée.'];
        yield ['name', 'Cette chaîne n\'est pas autorisée.'];
        yield ['nom', 'Cette chaîne n\'est pas autorisée.'];
        yield ['prénom', 'Cette chaîne n\'est pas autorisée.'];
        yield ['test', 'Cette chaîne n\'est pas autorisée.'];
    }

    /**
     * @dataProvider validNames
     */
    public function testValidNames(string $name): void
    {
        $formData = $this->getValidFormData();
        $formData['firstName'] = $name;
        $formData['lastName'] = $name;

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($formData);

        $this->assertFormValid($form);
    }

    /**
     * @return iterable<string[]>
     */
    public static function validNames(): iterable
    {
        yield ['Olivier'];
        yield ['Lïc'];
        yield ['Loïc'];
        yield ['éàïöôè'];
        yield ['Pierre-Emmanuel'];
        yield ['D\'Ouis-Pinçon'];
    }

    /**
     * @return array<string, mixed>
     */
    private function getValidFormData(): array
    {
        $faker = \Faker\Factory::create('fr_FR');

        return [
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
    }
}
