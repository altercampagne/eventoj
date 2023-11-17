<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connection');

        $client->submitForm('Se connecter', [
            '_username' => 'admin@altercampagne.ovh',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('homepage');
        $this->assertSelectorTextContains('#connected-as', 'Connecté en tant que Super admin');
    }
}
