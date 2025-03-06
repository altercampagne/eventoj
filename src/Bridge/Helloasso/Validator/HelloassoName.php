<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class HelloassoName extends Constraint
{
    public string $tooShortMessage = 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.';

    public string $noDigitMessage = 'Cette chaîne ne doit pas contenir de chiffres.';

    public string $noConsecutiveCharactersMessage = 'Cette chaîne ne semble pas valide.';

    public string $noVowelMessage = 'Cette chaîne ne semble pas valide.';

    public string $forbiddenValueMessage = 'Cette chaîne n\'est pas autorisée.';

    public string $forbiddenSpecialCharactersMessage = 'Cette chaîne contient des caractères spéciaux non autorisés.';
}
