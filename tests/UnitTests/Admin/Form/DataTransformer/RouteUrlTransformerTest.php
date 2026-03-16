<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Admin\Form\DataTransformer;

use App\Admin\Form\DataTransformer\RouteUrlTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RouteUrlTransformerTest extends TestCase
{
    #[DataProvider('validReverseTransformData')]
    public function testReverseTransform(string $input, string $expectedUrl): void
    {
        $this->assertSame($expectedUrl, new RouteUrlTransformer()->reverseTransform($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function validReverseTransformData(): iterable
    {
        yield 'Valid OpenRunner URL is not transformed' => [
            'https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric',
            'https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric',
        ];
        yield 'Valid openrunner iframe code return only URL' => [
            '<iframe width="100%" height="650" loading="lazy" src="https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric" style="border: none;"></iframe>',
            'https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric',
        ];
        yield 'Valid openrunner iframe code copy / pasted 2 times return only URL' => [
            '<iframe width="100%" height="650" loading="lazy" src="https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric" style="border: none;"></iframe><iframe width="100%" height="650" loading="lazy" src="https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric" style="border: none;"></iframe>',
            'https://www.openrunner.com/embed/5279777969664a517173474f626d6a37785375456d726372554e433665793458674d6478775a4a677942303d3a3ad843b0c76c11cb4474191d65fe1a9a90?unit=metric',
        ];

        yield 'Valid Komoot URL for tour is transformed in embedded' => [
            'https://www.komoot.com/fr-fr/tour/1271165736',
            'https://www.komoot.com/fr-fr/tour/1271165736/embed?profile=1',
        ];
        yield 'Valid Komoot URL is not transformed' => [
            'https://www.komoot.com/fr-fr/tour/1271165736/embed?profile=1&share_token=aQSp6fFyamvUyEqyFxgHC9Xe64Zdn93E27r0kSWgft7F4R8pSE',
            'https://www.komoot.com/fr-fr/tour/1271165736/embed?profile=1&share_token=aQSp6fFyamvUyEqyFxgHC9Xe64Zdn93E27r0kSWgft7F4R8pSE',
        ];
        yield 'Valid Komoot iframe code return only URL' => [
            '<iframe src="https://www.komoot.com/fr-fr/tour/1271165736/embed?profile=1&share_token=aQSp6fFyamvUyEqyFxgHC9Xe64Zdn93E27r0kSWgft7F4R8pSE" width="100%" height="700" frameborder="0" scrolling="no"></iframe>',
            'https://www.komoot.com/fr-fr/tour/1271165736/embed?profile=1&share_token=aQSp6fFyamvUyEqyFxgHC9Xe64Zdn93E27r0kSWgft7F4R8pSE',
        ];

        yield 'Valid OpenRunner iframe is transformed' => [
            '<iframe width="100%" height="650" loading="lazy" src="https://www.openrunner.com/en/embed/5378616f526831747578307a7a346c31466f375537393043616b744a63712b344437326d314371317251593d3a3ac12515c0f7c4961a9d836de199539427?unit=metric" style="border: none;"></iframe>',
            'https://www.openrunner.com/embed/5378616f526831747578307a7a346c31466f375537393043616b744a63712b344437326d314371317251593d3a3ac12515c0f7c4961a9d836de199539427?unit=metric',
        ];

        // Problème remonté par Mathieu en mars 2026
        // @see https://chat.altercampagne.ovh/channel/cercle-numerique?msg=5tuxaYyu5iHaRsCMn
        yield 'OpenRunner Enbed URL with code as query param' => [
            'https://www.openrunner.com/embed.html?code=5677516258434f54384b52724e584d6f5144425a4b4438354c767a735767695157506874573172376839673d3a3a1fdd217a73a8e94e11bffa705aa7b7c2&lang=fr&unit=metric',
            'https://www.openrunner.com/embed.html?code=5677516258434f54384b52724e584d6f5144425a4b4438354c767a735767695157506874573172376839673d3a3a1fdd217a73a8e94e11bffa705aa7b7c2&lang=fr&unit=metric',
        ];
        yield 'OpenRunner Enbed URL with code as query param & locale prefix' => [
            'https://www.openrunner.com/en/embed.html?code=5677516258434f54384b52724e584d6f5144425a4b4438354c767a735767695157506874573172376839673d3a3a1fdd217a73a8e94e11bffa705aa7b7c2&lang=fr&unit=metric',
            'https://www.openrunner.com/embed.html?code=5677516258434f54384b52724e584d6f5144425a4b4438354c767a735767695157506874573172376839673d3a3a1fdd217a73a8e94e11bffa705aa7b7c2&lang=fr&unit=metric',
        ];
    }
}
