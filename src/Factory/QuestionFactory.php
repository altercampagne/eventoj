<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Question;
use App\Entity\QuestionCategory;
use Zenstruck\Foundry\Object\Instantiator;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Question>
 */
final class QuestionFactory extends PersistentObjectFactory
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

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'category' => self::faker()->randomElement(QuestionCategory::cases()),
            'question' => self::faker()->text(),
            'answer' => self::faker()->text(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(Instantiator::withConstructor()->alwaysForce('locked'))
        ;
    }
}
