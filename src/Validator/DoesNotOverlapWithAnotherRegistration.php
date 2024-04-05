<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DoesNotOverlapWithAnotherRegistration extends Constraint
{
    public string $message = 'Tu es déjà inscrit pour tout ou partie de ces dates.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
