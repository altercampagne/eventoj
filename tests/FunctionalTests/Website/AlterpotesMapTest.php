<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Factory\MembershipFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlterpotesMapTest extends WebTestCase
{
    public function testWithUserWithoutMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();

        $client->loginUser($user);

        $client->request('GET', '/alterpotes');

        $this->assertResponseRedirects();
        $response = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_memberships');
        $this->assertSelectorTextContains('.alert-danger', 'Une adhésion à jour est nécessaire pour accéder à la carte des alterpotes !');
    }

    public function testWithMemberWithMembership(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();
        MembershipFactory::createOne(['user' => $user]);

        $client->loginUser($user);

        $client->request('GET', '/alterpotes');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('alterpotes_map');
    }
}
