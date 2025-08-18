<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form;

use App\Entity\User;
use App\Form\ProfileUpdateFormType;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class ProfileUpdateFormTypeTest extends KernelTestCase
{
    use FormAssertionsTrait;

    /** @var FormInterface<User> */
    private FormInterface $form;

    protected function setUp(): void
    {
        $user = new User();
        $user->setBirthDate(new \DateTimeImmutable('-20 years'));

        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ProfileUpdateFormType::class, $user, [
            'csrf_protection' => false,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            'diet' => 'vegetarian',
            'glutenIntolerant' => false,
            'lactoseIntolerant' => false,
            'dietDetails' => null,
            'hasDrivingLicence' => true,
            'biography' => "I'm only an humble test user.",
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithInvalidData(): void
    {
        $this->form->submit([
            'dietDetails' => 'This is a value which is waaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaay too long regarding the field data type in database.',
        ]);

        $this->assertFormInvalid($this->form, [
            'diet' => 'Le régime alimentaire est obligatoire.',
            'dietDetails' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
        ]);
    }
}
