<?php

declare(strict_types=1);

namespace App\Tests\UnitTests;

use Symfony\Component\Form\FormInterface;

trait FormAssertionsTrait
{
    /**
     * @param array<string, string> $expectedErrors
     */
    public function assertFormInvalid(FormInterface $form, array $expectedErrors = []): void
    {
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid());

        if (0 === \count($expectedErrors)) {
            return;
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()?->getName()] = $error->getMessage();
        }

        $this->assertSame($expectedErrors, $errors);
    }
}
