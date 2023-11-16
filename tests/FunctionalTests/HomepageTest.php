<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomepageTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertPageTitleContains('Évènements proposés par AlterCampagne');
        $this->assertSelectorTextContains('h1', 'Tous les évènements');
    }
}
