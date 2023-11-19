<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');

        $registrationEmail = $faker->email();

        $client->submitForm('Je m\'inscris', [
            'registration_form[email]' => $registrationEmail,
            'registration_form[name]' => $faker->name(),
            'registration_form[password]' => 'password',
        ]);

        $this->assertQueuedEmailCount(1);

        /* @phpstan-ignore-next-line */
        $email = $this->getMailerEvent()->getMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Merci de confirmer ton adresse mail.');
        $this->assertEmailAddressContains($email, 'to', $registrationEmail);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('registration_waiting_for_email_validation');
        $this->assertSelectorTextContains('h1', 'C\'est presque bon !');
    }
}
