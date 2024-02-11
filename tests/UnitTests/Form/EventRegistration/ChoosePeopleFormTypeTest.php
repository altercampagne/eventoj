<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\DataFixtures\Util\FixtureBuilder;
use App\Entity\Companion;
use App\Entity\Registration;
use App\Entity\User;
use App\Form\EventRegistration\ChoosePeopleFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\DatabaseUtilTrait;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChoosePeopleFormTypeTest extends KernelTestCase
{
    use DatabaseUtilTrait;
    use FormAssertionsTrait;

    private User $user;
    private FormInterface $form;

    protected function setUp(): void
    {
        $user = FixtureBuilder::createUser();
        $event = FixtureBuilder::createAT();
        $registration = new Registration($user, $event);
        $this->save($user, $event, $registration, FixtureBuilder::createCompanion(user: $user, children: false), FixtureBuilder::createCompanion(user: $user, children: true));

        $this->user = $user;

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        $this->form = $formFactory->create(ChoosePeopleFormType::class, new EventRegistrationDTO($registration), [
            'csrf_protection' => false,
            'registration' => $registration,
        ]);
    }

    public function testWithValidData(): void
    {
        $this->form->submit([
            'companions' => array_map(static function (Companion $companion): string {
                return (string) $companion->getId();
            }, $this->user->getCompanions()->toArray()),
            'neededBike' => 1,
        ]);
        $this->assertTrue($this->form->isValid());
    }

    public function testWithTooManyBikes(): void
    {
        $this->form->submit([
            'companions' => [],
            'neededBike' => 5,
        ]);

        $this->assertFormInvalid($this->form, [
            'neededBike' => 'Plus d\'un vélo par personne, ça fait beaucoup ! 🤯',
        ]);
    }
}
