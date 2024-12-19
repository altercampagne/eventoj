<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Form\EventRegistration;

use App\Entity\Companion;
use App\Entity\Registration;
use App\Entity\User;
use App\Factory\CompanionFactory;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use App\Form\EventRegistration\ChoosePeopleFormType;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Tests\DatabaseUtilTrait;
use App\Tests\UnitTests\FormAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Zenstruck\Foundry\Test\Factories;

class ChoosePeopleFormTypeTest extends KernelTestCase
{
    use DatabaseUtilTrait;
    use Factories;
    use FormAssertionsTrait;

    private User $user;
    private FormInterface $form;

    protected function setUp(): void
    {
        $user = UserFactory::createOne()->_real();
        CompanionFactory::new()->children()->create(['user' => $user]);
        CompanionFactory::new()->adult()->create(['user' => $user]);

        $event = EventFactory::new()->published()->withStages()->create()->_real();
        $registration = new Registration($user, $event);
        $this->save($registration);

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
