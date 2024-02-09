<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileCompanionTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testCreateAndUdateCompanion(): void
    {
        $client = static::createClient();

        $user = FixtureBuilder::createUser();
        $this->save($user);

        $client->loginUser($user);

        $client->request('GET', '/me/companions');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes compagnon·es');

        $this->assertSelectorExists('#test-no-companion-text');

        $client->clickLink('Ajouter une·e compagnon·e');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_create');

        $client->submitForm('C\'est tout bon !', [
            'companion_form[firstName]' => 'Companion',
            'companion_form[lastName]' => 'ForTests',
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
        $this->assertSelectorTextContains('.alert-success', 'Companion ForTests est à jour !');
        $this->assertSelectorTextContains('.card-body', "Pas d'information de contact");

        $client->clickLink('Modifier Companion ForTests');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_companion_update');

        $client->submitForm('C\'est tout bon !', [
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
        $this->assertSelectorTextContains('.alert-success', 'Companion ForTests est à jour !');
        $this->assertSelectorTextContains('.card-body', 'contact@yopmail.com');
        $this->assertSelectorTextContains('.card-body', '+33 6 06 06 06 06');
    }
}
