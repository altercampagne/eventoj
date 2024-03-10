<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Entity\User;
use App\Form\ProfileUpdateFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ProfileUpdateFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    private FormInterface $form;

    protected function setUp(): void
    {
        $user = new User();
        $user->setBirthDate(new \DateTimeImmutable('-20 years'));

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ProfileUpdateFormType::class, $user, [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $this->form->submit([
            'diet' => 'vegetarian',
            'glutenIntolerant' => false,
            'lactoseIntolerant' => false,
            'dietDetails' => null,
            'hasDrivingLicence' => true,
            'biography' => 'I\'m only an humble test user.',
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithInvalidDates(): void
    {
        $this->form->submit([]);

        $this->assertFormInvalid($this->form, [
            'diet' => 'Le régime alimentaire est obligatoire.',
        ]);
    }
}
