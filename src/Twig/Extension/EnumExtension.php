<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Attribute\AsTwigFunction;

/**
 * @see https://github.com/twigphp/Twig/issues/3681#issuecomment-1162728959
 */
class EnumExtension
{
    #[AsTwigFunction('enum')]
    public function enum(string $enumFQN): object
    {
        return new readonly class($enumFQN) {
            public function __construct(private string $enum)
            {
                if (!enum_exists($this->enum)) {
                    throw new \InvalidArgumentException("$this->enum is not an Enum type and cannot be used in this function");
                }
            }

            /**
             * @param mixed[] $arguments
             */
            public function __call(string $name, array $arguments): mixed
            {
                $enumFQN = \sprintf('%s::%s', $this->enum, $name);

                if (\defined($enumFQN)) {
                    return \constant($enumFQN);
                }

                if (method_exists($this->enum, $name)) {
                    return $this->enum::$name(...$arguments);
                }

                throw new \BadMethodCallException("Neither \"{$enumFQN}\" nor \"{$enumFQN}::{$name}()\" exist in this runtime.");
            }
        };
    }
}
