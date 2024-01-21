<?php

declare(strict_types=1);

namespace App\Tests\UnitTests;

use Symfony\Component\Form\FormInterface;

trait FormAssertionsTrait
{
    /**
     * @param array<string, string|string[]> $expectedErrors
     */
    public function assertFormInvalid(FormInterface $form, array $expectedErrors = []): void
    {
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        if (0 === \count($expectedErrors)) {
            return;
        }

        $errors = $this->getFlattenErrors($form);

        foreach ($expectedErrors as $field => $expectedErrorsForField) {
            $this->assertTrue(\array_key_exists($field, $errors), "No error found for field $field");
            $expectedErrorsForField = (array) $expectedErrorsForField;

            $this->assertSame($expectedErrorsForField, $errors[$field]);
        }
    }

    public function assertFormValid(FormInterface $form): void
    {
        $this->assertTrue($form->isSynchronized());
        $this->assertSame([], $this->getFlattenErrors($form));
        $this->assertTrue($form->isValid());
    }

    /**
     * @return array<string, string[]>
     */
    private function getFlattenErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()?->getName()][] = $error->getMessage();
        }

        return $errors;
    }
}
