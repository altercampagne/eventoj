<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class EventCreateAndUpdateTest extends WebTestCase
{
    use Factories;

    public function testCreateAndUdateAT(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->admin()->create()->_real();

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

        $client->submitForm('J\'ai bien compris et je confirme la publication de l\'évènement.');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-danger', 'L\'évènement Nouveau nom d\'évènement n\'a aucune étape de définie pour le moment !');
    }
}
