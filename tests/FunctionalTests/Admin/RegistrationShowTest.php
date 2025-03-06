<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\RegistrationFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class RegistrationShowTest extends WebTestCase
{
    use Factories;

    public function testShowRegistration(): void
    {
        $client = static::createClient();

        $registration = RegistrationFactory::new()->confirmed()->create();

        $client->loginUser(UserFactory::new()->admin()->create()->_real());

        $client->request('GET', "/_admin/registrations/{$registration->getId()}");

        $this->assertResponseIsSuccessful();

        $client->submitForm('Renvoyer le mail de confirmation');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.alert-success', 'Le mail de confirmation a bien été envoyé.');
    }
}
