<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlternativeCreateAndUpdateTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testCreateAndUdateAT(): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $client = static::createClient();

        $user = FixtureBuilder::createUser(admin: true);
        $this->save($user);

        $client->loginUser($user);

        $client->request('GET', '/_admin/alternatives/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'une alternative');

        $client->submitForm('Ajouter', [
            'alternative_form[name]' => 'alternative de test',
            'alternative_form[description]' => 'Juste un truc de test.',
            'alternative_form[address][countryCode]' => 'FR',
            'alternative_form[address][addressLine1]' => $faker->address(),
            'alternative_form[address][zipCode]' => $faker->postCode(),
            'alternative_form[address][city]' => $faker->city(),
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_alternative_list');
        $this->assertSelectorTextContains('.alert-success', 'L\'alternative a bien été créée ! 🥳');

        $client->request('GET', '/_admin/alternatives/alternative-de-test/update');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier', [
            'alternative_form[name]' => 'Nouveau nom d\'alternative',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_alternative_list');
        $this->assertSelectorTextContains('.alert-success', 'L\'alternative a bien été modifiée ! 🥳');
    }
}
