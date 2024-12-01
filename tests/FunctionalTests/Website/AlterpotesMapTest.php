<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\DataFixtures\Util\FixtureBuilder;
use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlterpotesMapTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testWithUserWithoutMembership(): void
    {
        $client = static::createClient();

        $user = FixtureBuilder::createUser();
        $this->save($user);

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

        $user = FixtureBuilder::createUser();
        $membership = FixtureBuilder::createMembershipForUser($user);
        $this->save($user, $membership->getPayment(), $membership);
        $this->getEntityManager()->clear();

        // dd($user->isMember());

        $client->loginUser($user);

        $client->request('GET', '/alterpotes');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('alterpotes_map');
    }
}
