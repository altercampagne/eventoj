<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class UserShowTest extends WebTestCase
{
    use Factories;

    public function testShowAndVerifyMail(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->create();

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('GET', '/_admin/users/'.$user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('i.fa-solid.fa-at.text-danger');
        $this->assertSelectorTextContains("button#button-user-verify-email-{$user->getId()}", 'Adresse mail vérifiée ! 👌');

        $client->submitForm("Confirmer qu'il s'agit d'une adresse mail vérifiée");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_user_show');
        $this->assertSelectorExists('i.fa-solid.fa-at.text-success');
        $this->assertSelectorNotExists("button#button-user-verify-email-{$user->getId()}");
    }

    public function testCreateMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->create();

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('GET', '/_admin/users/'.$user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('i.fa-solid.fa-ticket-simple.text-danger');

        $client->submitForm("Confirmer la création de l'adhésion");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_user_show');
        $this->assertSelectorExists('i.fa-solid.fa-ticket-simple.text-success');
        $this->assertSelectorTextContains('.alert-success', "Adhésion créée avec succès pour {$user->getFullName()} !");

        $client->submitForm("Confirmer la prolongation de l'adhésion");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_user_show');
        $this->assertSelectorTextContains('.alert-success', "Adhésion prolongée avec succès pour {$user->getFullName()} !");

        $client->submitForm("Confirmer la prolongation de l'adhésion");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_user_show');
        $this->assertSelectorTextContains('.alert-danger', "{$user->getFullName()} est encore membre pour plus d'un an, impossible de prolonger son adhésion.");
    }

    public function testRemoveDietDetails(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->create(['dietDetails' => 'Blablabla']);

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('GET', '/_admin/users/'.$user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists("span[data-test-person-id=\"{$user->getId()}\"]");
        $this->assertSelectorTextContains("span[data-test-person-id=\"{$user->getId()}\"]", 'Blablabla');

        $client->submitForm('Confirmer la suppression du régime particulier');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('admin_user_show');
        $this->assertSelectorExists('div.alert.alert-success');
        $this->assertSelectorTextContains('div.alert.alert-success', "Le régime particulier de {$user->getFullName()} a bien été supprimé !");
        $this->assertSelectorNotExists("span[data-test-person-id=\"{$user->getId()}\"]");
    }
}
