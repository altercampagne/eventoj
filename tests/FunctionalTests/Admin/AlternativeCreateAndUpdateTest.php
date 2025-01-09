<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlternativeCreateAndUpdateTest extends WebTestCase
{
    public function testCreateAndUdateAT(): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $client = static::createClient();

        $user = UserFactory::new()->admin()->create()->_real();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/_admin/alternatives/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'une alternative');

        $form = $crawler->selectButton('Ajouter')->form();
        /* @phpstan-ignore-next-line */
        $form['alternative_form[categories][0]']->tick();
        /* @phpstan-ignore-next-line */
        $form['alternative_form[categories][3]']->tick();

        $client->submit($form, [
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
        $this->assertRouteSame('admin_alternative_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'alternative a bien été créée ! 🥳');

        $client->clickLink('Modifier l\'alternative');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier', [
            'alternative_form[name]' => 'Nouveau nom d\'alternative',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_alternative_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'alternative a bien été modifiée ! 🥳');
    }
}
