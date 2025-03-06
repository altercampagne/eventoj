<?php

declare(strict_types=1);

namespace App\Bridge\Helloasso\Validator;

use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @see https://drive.google.com/file/d/1oFciovXkKiMJ7EIDcNYh5nVTq5NLPIEI/view
 */
class HelloassoNameValidator extends ConstraintValidator
{
    private const array FORBIDDEN_VALUES = ['firstname', 'lastname', 'unknown', 'first_name', 'last_name', 'anonyme', 'user', 'admin', 'name', 'nom', 'prénom', 'test'];

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof HelloassoName) {
            throw new UnexpectedTypeException($constraint, HelloassoName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            return;
        }

        $string = new UnicodeString($value);

        if (2 > $string->length()) {
            $this->context->buildViolation($constraint->tooShortMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if ($string->match('/\d+/')) {
            $this->context->buildViolation($constraint->noDigitMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if ($string->match('/([[:alnum:]])\1\1/i')) {
            $this->context->buildViolation($constraint->noConsecutiveCharactersMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if (!$string->match('/[aeiouyáâàåäðéêèëíîìïóôòøõöúûùü]/i')) {
            $this->context->buildViolation($constraint->noVowelMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if (\in_array(mb_strtolower($value), self::FORBIDDEN_VALUES)) {
            $this->context->buildViolation($constraint->forbiddenValueMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        // if (!$string->match("/^[[:alnum:]'ç-]*$/i")) {
        // @see https://www.php.net/manual/en/regexp.reference.unicode.php
        if (!$string->match("/^[\p{L}'ç -]*$/i")) {
            $this->context->buildViolation($constraint->forbiddenSpecialCharactersMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        if ($string->match('/--/')) {
            $this->context->buildViolation($constraint->forbiddenSpecialCharactersMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }
    }
}
