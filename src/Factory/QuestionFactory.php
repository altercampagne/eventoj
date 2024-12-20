<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Question;
use App\Entity\QuestionCategory;
use Zenstruck\Foundry\Object\Instantiator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Question>
 */
final class QuestionFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Question::class;
    }

    public function locked(): self
    {
        return $this->with([
            'locked' => true,
        ]);
    }

    protected function defaults(): array|callable
    {
        return [
            'category' => self::faker()->randomElement(QuestionCategory::cases()),
            'question' => self::faker()->text(),
            'answer' => self::faker()->text(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->instantiateWith(Instantiator::withConstructor()->alwaysForce('locked'))
        ;
    }
}
