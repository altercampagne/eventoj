<?php

declare(strict_types=1);

namespace App\Tests\UnitTests;

use Symfony\Component\Form\FormInterface;

trait FormAssertionsTrait
{
    /**
     * @template TData
     *
     * @param FormInterface<TData>           $form
     * @param array<string, string|string[]> $expectedErrors
     */
    public function assertFormInvalid(FormInterface $form, array $expectedErrors = []): void
    {
        $this->assertTrue($form->isSubmitted(), 'Form has not been submitted!');
        $this->assertTrue($form->isSynchronized(), 'Form is not synchronized!');
        $this->assertFalse($form->isValid(), 'Form is valid!');

        if ([] === $expectedErrors) {
            return;
        }

        $errors = $this->getFlattenErrors($form);

        foreach ($expectedErrors as $field => $expectedErrorsForField) {
            $this->assertTrue(\array_key_exists($field, $errors), "No error found for field {$field}. Existing errors : ".print_r($errors, true));
            $expectedErrorsForField = (array) $expectedErrorsForField;

            $this->assertSame($expectedErrorsForField, $errors[$field]);
        }
    }

    /**
     * @template TData
     *
     * @param FormInterface<TData> $form
     */
    public function assertFormValid(FormInterface $form): void
    {
        $this->assertTrue($form->isSynchronized());
        $this->assertSame([], $this->getFlattenErrors($form));
        $this->assertTrue($form->isValid());
    }

    /**
     * @template TData
     *
     * @param FormInterface<TData> $form
     *
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
