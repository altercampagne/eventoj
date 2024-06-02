<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Meal;
use App\Form\EventRegistration\EventRegistrationDTO;
use App\Service\UserEventRegistration\UserEventComputedRegistrations;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DoesNotOverlapWithAnotherRegistrationValidator extends ConstraintValidator
{
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

        $userEventComputedRegistrations = new UserEventComputedRegistrations($value->registration->getUser(), $value->registration->getEvent());

        foreach ($value->getBookedStages() as $bookedStage) {
            if ($value->stageStart === $bookedStage) {
                $meals = $value->getFirstDayMeals();
            } elseif ($value->stageEnd === $bookedStage) {
                $meals = $value->getLastDayMeals();
            } else {
                $meals = Meal::cases();
            }

            foreach ($meals as $meal) {
                if ($userEventComputedRegistrations->hasRegistrationForStageAndMeal($bookedStage, $meal)) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation();

                    return;
                }
            }
        }
    }
}
