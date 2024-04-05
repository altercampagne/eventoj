<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Registration;
use App\Form\EventRegistration\EventRegistrationDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DoesNotOverlapWithAnotherRegistrationValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DoesNotOverlapWithAnotherRegistration) {
            throw new UnexpectedTypeException($constraint, DoesNotOverlapWithAnotherRegistration::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof EventRegistrationDTO) {
            throw new UnexpectedValueException($value, EventRegistrationDTO::class);
        }

        $event = $value->registration->getEvent();
        $user = $value->registration->getUser();

        $confirmedStages = $this->em->getRepository(Registration::class)->findConfirmedStagesForEventAndUser($event, $user);
        if (0 === \count($confirmedStages)) {
            return;
        }

        foreach ($value->getBookedStages() as $bookedStage) {
            if (!\in_array($bookedStage, $confirmedStages)) {
                continue;
            }

            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }
    }
}
