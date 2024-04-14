<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionCRUDTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testCreateAndUdateAT(): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $client = static::createClient();

        $user = FixtureBuilder::createUser(admin: true);
        $this->save($user);

        $client->loginUser($user);

        $client->request('GET', '/_admin/questions/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'une question');

        $client->submitForm('Ajouter', [
            'question_form[question]' => 'Quelle est la réponse ?',
            'question_form[answer]' => 'Voilà la réponse',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_question_show');
        $this->assertSelectorTextContains('.alert-success', 'La question a bien été créée ! 🥳');

        $client->clickLink('Modifier la question');
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier', [
            'question_form[answer]' => '42',
        ]);

        $this->assertSelectorNotExists('.invalid-feedback', 'Form contains errors');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_question_show');
        $this->assertSelectorTextContains('.alert-success', 'La question a bien été modifiée ! 🥳');

        $client->submitForm('J\'ai bien compris et je confirme la suppression définitive', []);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_question_list');
        $this->assertSelectorTextContains('.alert-success', 'La question a été supprimée !');
    }
}
