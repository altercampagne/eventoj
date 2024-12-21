<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserShowTest extends WebTestCase
{
    public function testShowAndVerifyMail(): void
    {
        $client = static::createClient();

        $user = UserFactory::new()->create()->_real();

        $client->loginUser(UserFactory::new()->admin()->create()->_real());

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
}
