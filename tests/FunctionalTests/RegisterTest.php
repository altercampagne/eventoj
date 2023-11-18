<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');

        $client->submitForm('Je m\'inscris', [
            'registration_form[email]' => 'georges.abitnol@gmail.com',
            'registration_form[name]' => 'Georges Abitbol',
            'registration_form[password]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('registration_waiting_for_email_validation');
        $this->assertSelectorTextContains('h1', 'C\'est presque bon !');
    }
}
