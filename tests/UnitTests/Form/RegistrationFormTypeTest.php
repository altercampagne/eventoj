<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\RegistrationFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Zenstruck\Foundry\Test\Factories;

class RegistrationFormTypeTest extends KernelTestCase
{
    use Factories;

    use FormAssertionsTrait;

    public function testSubmitValidData(): void
    {
        $form = $this->getForm();
        $form->submit($this->getValidFormData());

        $this->assertFormValid($form);
    }

    public function testExistingEmailDoesNotWork(): void
    {
        $form = $this->getForm();
        $form->submit(array_merge($this->getValidFormData(), [
            'email' => UserFactory::createOne()->getEmail(),
        ]));

        $this->assertFormInvalid($form, [
            'email' => 'Il y a déjà un compte avec cette adresse mail',
        ]);
    }

    public function testInvalidZipCode(): void
    {
        $data = $this->getValidFormData();
        /* @phpstan-ignore-next-line */
        $data['address']['countryCode'] = 'FR';
        /* @phpstan-ignore-next-line */
        $data['address']['zipCode'] = 123;

        $form = $this->getForm();
        $form->submit($data);

        $this->assertFormInvalid($form, [
            'zipCode' => "Ce code postal n'est pas valide.",
        ]);
    }

    public function testSameFirstAndLastName(): void
    {
        $data = $this->getValidFormData();
        $data['firstName'] = 'foobar';
        $data['lastName'] = 'foobar';

        $form = $this->getForm();
        $form->submit($data);

        $this->assertFormInvalid($form, [
            'registration_form' => 'Le prénom et le nom de famille ne doivent pas être identiques.',
        ]);
    }

    #[DataProvider('invalidDataProvider')]
    public function testVariousErrors(string $field, ?string $value, string $expectedError): void
    {
        $form = $this->getForm();
        $form->submit(array_merge($this->getValidFormData(), [
            $field => $value,
        ]));

        $this->assertFormInvalid($form, [
            $field => $expectedError,
        ]);
    }

    /**
     * @return iterable<array{string, ?string, string}>
     */
    public static function invalidDataProvider(): iterable
    {
        // In order to correctly handle null (or empty) firstName / lastName,
        // we have to create a dedicated data class for the form.
        // yield ['firstName', null, 'Cette valeur ne doit pas être vide.'];
        // yield ['lastName', null, 'Cette valeur ne doit pas être vide.'];

        yield ['plainPassword', 'bla', 'Ton mot de passe doit faire au moins 7 caractères.'];
        yield ['birthDate', new \DateTimeImmutable('-10 years')->format('Y-m-d'), 'Tu dois être majeur pour pouvoir t\'inscrire.'];
        yield ['birthDate', new \DateTimeImmutable('-130 years')->format('Y-m-d'), 'Une vraie date de naissance, ce serait mieux ! :)'];
    }

    #[DataProvider('invalidNames')]
    public function testInvalidNames(mixed $name, string $expectedError): void
    {
        $form = $this->getForm();
        $form->submit(array_merge($this->getValidFormData(), [
            'firstName' => $name,
            'lastName' => $name,
        ]));

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
        // In order to correctly handle null (or empty) firstName / lastName,
        // we have to create a dedicated data class for the form.
        // yield [null, 'Cette valeur ne doit pas être vide.'];
        // yield ['', 'Cette valeur ne doit pas être vide.'];

        yield ['a', 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.'];
        yield ['Coucou3fail', 'Cette chaîne ne doit pas contenir de chiffres.'];
        yield ['COuUulala', 'Cette chaîne ne semble pas valide.'];
        yield ['PsDVll', 'Cette chaîne ne semble pas valide.'];

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
        yield ['firsTNAme', 'Cette chaîne n\'est pas autorisée.'];
        yield ['Lastname', 'Cette chaîne n\'est pas autorisée.'];

        // Valid name but not accepted by helloasso (yet!)
        yield ['last--name', 'Cette chaîne contient des caractères spéciaux non autorisés.'];
    }

    #[DataProvider('validNames')]
    public function testValidFirstName(string $name): void
    {
        $form = $this->getForm();
        $form->submit(array_merge($this->getValidFormData(), [
            'firstName' => $name,
        ]));

        $this->assertFormValid($form);
    }

    #[DataProvider('validNames')]
    public function testValidLastName(string $name): void
    {
        $form = $this->getForm();
        $form->submit(array_merge($this->getValidFormData(), [
            'lastName' => $name,
        ]));

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
        yield ['Le Roy'];
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
            'birthDate' => new \DateTimeImmutable('-20 years')->format('Y-m-d'),
            'email' => $faker->email(),
            'phoneNumber' => '0606060606',
            'address' => [
                'countryCode' => 'FR',
                'addressLine1' => $faker->address(),
                'zipCode' => $faker->postCode(),
                'city' => $faker->city(),
            ],
            'plainPassword' => 'password',
        ];
    }

    /**
     * @return FormInterface<User>
     */
    private function getForm(): FormInterface
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        /** @var FormInterface<User> $form */
        $form = $formFactory->create(RegistrationFormType::class, null, [
            'csrf_protection' => false,
        ]);

        return $form;
    }
}
