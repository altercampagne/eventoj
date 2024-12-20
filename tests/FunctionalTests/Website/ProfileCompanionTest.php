<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Entity\Diet;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileCompanionTest extends WebTestCase
{
    public function testCreateAndUdateCompanion(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();

        $client->loginUser($user);

        $client->request('GET', '/me/companions');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes compagnon·es');

        $this->assertSelectorExists('#test-no-companion-text');

        $client->clickLink('Ajouter une·e compagnon·e');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_create');

        $client->submitForm('Ajouter', [
            'companion_form[firstName]' => 'Companion',
            'companion_form[lastName]' => 'ForTests',
            'companion_form[email]' => 'contact@yopmail.com',
            'companion_form[birthDate]' => (new \DateTimeImmutable('-11 years'))->format('Y-m-d'),
            'companion_form[diet]' => 'vegetarian',
            'companion_form[glutenIntolerant]' => false,
            'companion_form[lactoseIntolerant]' => false,
            'companion_form[dietDetails]' => null,
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companions');
        $this->assertSelectorTextContains('.alert-success', 'Companion ForTests a bien été ajouté·e !');

        $client->clickLink('Modifier Companion ForTests');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_update');

        $client->submitForm('Modifier', [
            'companion_form[firstName]' => 'Companion',
            'companion_form[lastName]' => 'ForTests',
            'companion_form[birthDate]' => (new \DateTimeImmutable('-11 years'))->format('Y-m-d'),
            'companion_form[email]' => 'contact@yopmail.com',
            'companion_form[phoneNumber]' => '0606060606',
            'companion_form[diet]' => 'vegetarian',
            'companion_form[glutenIntolerant]' => false,
            'companion_form[lactoseIntolerant]' => false,
            'companion_form[dietDetails]' => null,
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companions');
        $this->assertSelectorTextContains('.alert-success', 'Companion ForTests a bien été modifié·e !');
        $this->assertSelectorTextContains('.card-body', 'contact@yopmail.com');
        $this->assertSelectorTextContains('.card-body', '+33 6 06 06 06 06');
    }

    public function testCreateAndUpdateFromEventRegisterGoesBackToEvent(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne(['diet' => Diet::VEGETARIAN])->_real();
        $event = EventFactory::new()->published()->withRandomStages()->create()->_real();
        $client->loginUser($user);

        $client->request('GET', "/event/{$event->getSlug()}/register");

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register');

        $this->assertSelectorNotExists('input[data-fullname="Companion ForTests"]');

        $client->clickLink('Ajouter un·e compagnon·e');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_create');

        $client->submitForm('Ajouter', [
            'companion_form[firstName]' => 'Companion',
            'companion_form[lastName]' => 'ForTests',
            'companion_form[email]' => 'contact@yopmail.com',
            'companion_form[birthDate]' => (new \DateTimeImmutable('-11 years'))->format('Y-m-d'),
            'companion_form[diet]' => 'vegetarian',
            'companion_form[glutenIntolerant]' => false,
            'companion_form[lactoseIntolerant]' => false,
            'companion_form[dietDetails]' => null,
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register');

        $this->assertSelectorTextContains('.alert-success', 'Companion ForTests a bien été ajouté·e !');
        $this->assertSelectorExists('input[data-fullname="Companion ForTests"]');

        $client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_update');

        $client->submitForm('Modifier', [
            'companion_form[firstName]' => 'UpdatedCompanion',
            'companion_form[lastName]' => 'ForUpdateTests',
            'companion_form[email]' => 'contact@yopmail.com',
            'companion_form[birthDate]' => (new \DateTimeImmutable('-11 years'))->format('Y-m-d'),
            'companion_form[diet]' => 'vegetarian',
            'companion_form[glutenIntolerant]' => false,
            'companion_form[lactoseIntolerant]' => false,
            'companion_form[dietDetails]' => null,
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('event_register');

        $this->assertSelectorTextContains('.alert-success', 'UpdatedCompanion ForUpdateTests a bien été modifié·e !');
        $this->assertSelectorExists('input[data-fullname="UpdatedCompanion ForUpdateTests"]');
    }
}
