<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Factory\MembershipFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class ProfileMembershipsTest extends WebTestCase
{
    use Factories;

    public function testWithActiveMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        MembershipFactory::createOne(['user' => $user]);

        $client->loginUser($user);

        $client->request('GET', '/me/memberships');

        $year = (int) date('Y');
        if (7 > (int) date('m')) {
            --$year;
        }

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes adhésions');
        $this->assertSelectorTextContains('.bg-success-subtle', \sprintf('Adhésion du 1 juillet %d au 30 juin %d', $year, $year + 1));
        $this->assertSelectorTextContains('.bg-success', 'En cours');
    }

    public function testWithoutActiveMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();

        $client->loginUser($user);

        $client->request('GET', '/me');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-danger', "Tu n'es actuellement pas membre de l'association.");

        $client->clickLink('Gérer les adhésions');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_memberships');
        $this->assertSelectorTextContains('h1', 'Tes adhésions');
        $this->assertSelectorTextContains('.alert-danger', "Tu n'es actuellement pas membre de l'association.");

        $client->submitForm('Adhérer à l\'association');

        // Redirection fails because we're not in https in tests
        // $this->assertResponseRedirects();
        // $client->followRedirect();
        // $this->assertResponseIsSuccessful();
        // $this->assertRouteSame('payment_initiate_membership_payment');
    }

    public function testInitiatePaymentWithActiveMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne();
        MembershipFactory::createOne(['user' => $user]);

        $client->loginUser($user);

        $client->request('POST', '/payment/initiate_membership_payment');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('profile_memberships');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes adhésions');
    }
}
