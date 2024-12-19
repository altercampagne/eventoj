<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Se connecter');

        $client->submitForm('Se connecter', [
            '_username' => UserFactory::createOne()->getEmail(),
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects();
        $response = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('homepage');
        $this->assertSelectorExists('#connected-as');
    }
}
