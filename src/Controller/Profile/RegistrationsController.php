<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/registrations', name: 'profile_registrations')]
class RegistrationsController extends AbstractController
{
    public function __invoke(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTimeImmutable();
        $coming = [];
        $past = [];
        foreach ($user->getRegistrations() as $registration) {
            if ($registration->isWaitingPayment()) {
                continue;
            }

            if (null === $stageRegistration = $registration->getStageRegistrationEnd()) {
                throw new \LogicException('Found a confirmed registration without stages!');
            }

            if ($stageRegistration->getStage()->getDate() < $now) {
                $past[] = $registration;
            } else {
                $coming[] = $registration;
            }
        }

        return $this->render('profile/registrations.html.twig', [
            'coming_registrations' => $coming,
            'past_registrations' => $past,
        ]);
    }
}
