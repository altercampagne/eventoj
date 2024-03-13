<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StageCreateAndUpdateTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testCreateAndUdateAT(): void
    {
        $client = static::createClient();

        $user = FixtureBuilder::createUser(admin: true);
        $event = FixtureBuilder::createAT(published: false);
        $this->save($user, $event);

        $client->loginUser($user);

        $client->request('GET', '/_admin/stages/create/'.$event->getSlug());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'une étape pour '.$event->getName());

        $client->submitForm('Ajouter', [
            'stage_form[type]' => 'before',
            'stage_form[date]' => '2025-06-01',
            'stage_form[name]' => 'étape de test',
            'stage_form[description]' => 'Juste un truc de test.',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'étape a bien été créée ! 🥳');

        $client->request('GET', '/_admin/stages/etape-de-test/update');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier', [
            'stage_form[name]' => 'Nouveau nom d\'étape',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_event_show');
        $this->assertSelectorTextContains('.alert-success', 'L\'étape a bien été modifiée ! 🥳');
    }
}
