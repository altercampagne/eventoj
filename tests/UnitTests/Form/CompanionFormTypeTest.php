<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Entity\Companion;
use App\Entity\User;
use App\Form\CompanionFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CompanionFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    /** @var FormInterface<Companion> */
    private FormInterface $form;

    protected function setUp(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(CompanionFormType::class, new Companion(new User()), [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $this->form->submit([
            'firstName' => $faker->firstName(),
            'lastName' => $faker->lastName(),
            'birthDate' => new \DateTimeImmutable('-20 years')->format('Y-m-d'),
            'email' => $faker->email(),
            'phoneNumber' => '0606060606',
            'diet' => 'vegetarian',
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithInvalidDates(): void
    {
        $this->form->submit([
            'firstName' => 'test',
            'lastName' => 'youhou',
            'birthDate' => new \DateTimeImmutable('+20 years')->format('Y-m-d'),
            'email' => 'lalala',
            'phoneNumber' => '0606',
            'diet' => 'fail',
        ]);

        $this->assertFormInvalid($this->form, [
            'firstName' => 'Cette chaîne n\'est pas autorisée.',
            'birthDate' => 'Une date de naissance dans le futur, ça ne va pas être possible !',
            'phoneNumber' => 'Cette valeur n\'est pas un numéro de téléphone valide.',
            'diet' => 'Le choix sélectionné est invalide.',
        ]);
    }
}
