<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Tests\DatabaseUtilTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileUpdateProfileTest extends WebTestCase
{
    use DatabaseUtilTrait;

    public function testUpdate(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $client = static::createClient();

        $user = $this->getRandomUser();

        $client->loginUser($user);

        $client->request('GET', '/me/profile');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mise à jour de ton profil');

        $client->submitForm('Mettre à jour', [
            'profile_update_form[diet]' => 'vegetarian',
            'profile_update_form[glutenIntolerant]' => false,
            'profile_update_form[lactoseIntolerant]' => false,
            'profile_update_form[dietDetails]' => null,
            'profile_update_form[hasDrivingLicence]' => true,
            'profile_update_form[biography]' => 'O\'m only an humble test user.',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('profile_update_profile');
        $this->assertSelectorTextContains('.alert-success', 'Ton profil a bien été mis à jour !');
    }
}
