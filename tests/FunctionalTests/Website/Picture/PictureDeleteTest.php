<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website\Picture;

use App\Factory\Document\EventPictureFactory;
use App\Factory\EventFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class PictureDeleteTest extends WebTestCase
{
    use Factories;

    public function testRegistration(): void
    {
        $client = static::createClient();

        $event = EventFactory::new()->published()->withRandomStages()->create();
        $picture = EventPictureFactory::createOne(['event' => $event]);

        $client->loginUser($picture->getUser());

        $client->request('POST', "/pictures/{$picture->getId()}/delete");

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('pictures_upload');
    }
}
