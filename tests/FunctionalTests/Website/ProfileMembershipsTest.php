<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Factory\MembershipFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileMembershipsTest extends WebTestCase
{
    public function testWithActiveMembership(): void
    {
        $user = UserFactory::createOne()->_real();
        MembershipFactory::createOne(['user' => $user]);

        $client = static::createClient();

        $client->loginUser($user);

        $client->request('GET', '/me/memberships');

        $year = (int) date('Y');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes adhésions');
        $this->assertSelectorTextContains('.bg-success-subtle', \sprintf('Adhésion du 1 mai %d au 30 avril %d', $year, $year + 1));
        $this->assertSelectorTextContains('.bg-success', 'En cours');
    }

    public function testWithoutActiveMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();

        $client->loginUser($user);

        $client->request('GET', '/me/memberships');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Tes adhésions');
        $this->assertSelectorTextContains('.alert-danger', 'Tu n\'es actuellement pas membre de l\'association.');

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

        $user = UserFactory::createOne()->_real();
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
