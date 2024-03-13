<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventCreateAndUpdateTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testCreateAndUdateAT(): void
    {
        $client = static::createClient();

        $user = FixtureBuilder::createUser(admin: true);
        $this->save($user);

        $client->loginUser($user);

        $client->request('GET', '/_admin/events/create/AT');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'un évènement de type AlterTour');

        $client->submitForm('Ajouter', [
            'event_form[name]' => 'évènement de test',
            'event_form[description]' => 'Juste un truc de test.',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'évènement a bien été créé ! 🥳');

        $client->clickLink('Modifier l\'évènement');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_update');

        $client->submitForm('Modifier', [
            'event_form[name]' => 'Nouveau nom d\'évènement',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'évènement a bien été modifié ! 🥳');
    }
}
