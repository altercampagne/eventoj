<?php

declare(strict_types=1);

namespace App\Admin\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<string, string>
 */
class RouteUrlTransformer implements DataTransformerInterface
{
    /**
     * We don't need to transform the stored URL.
     *
     * @param ?string $value
     */
    public function transform(mixed $value): string
    {
        return (string) $value;
    }

    /**
     * Transforms a string (embed code or URL) to an URL.
     *
     * @param ?string $value
     *
     * @throws TransformationFailedException if input cannot be converted to an URL
     */
    public function reverseTransform(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        if (str_starts_with($value, 'https://www.openrunner.com/embed')) {
            return $value;
        }

        if (preg_match('#^https://www.komoot.com/.*/embed?.*share_token=.*$#', $value)) {
            return $value;
        }

        if (!preg_match('#^<iframe.*src="([^ "]*)".*></iframe>#', $value, $matches)) {
            throw new TransformationFailedException("Unable to extract URL from $value");
        }

        return $matches[1];
    }
}
