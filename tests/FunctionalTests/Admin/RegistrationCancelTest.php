<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Admin;

use App\Factory\PaymentFactory;
use App\Factory\RegistrationFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class RegistrationCancelTest extends WebTestCase
{
    use Factories;

    public function testCancelRegistrationWithoutPayments(): void
    {
        $client = static::createClient();

        $registration = RegistrationFactory::new()->confirmed()->create();

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/cancel");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.alert-info', "L'inscription a bien été annulée !");
    }

    public function testCancelRegistrationWithPayments(): void
    {
        $client = static::createClient();

        $registration = RegistrationFactory::new()->confirmed()->create();
        PaymentFactory::new(['registration' => $registration])->approved()->create();
        PaymentFactory::new(['registration' => $registration])->approved()->withInstalments()->create();

        $client->loginUser(UserFactory::new()->admin()->create());

        $client->request('POST', "/_admin/registrations/{$registration->getId()}/cancel");

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.alert-info', "L'inscription a bien été annulée (et remboursée) !");
    }
}
