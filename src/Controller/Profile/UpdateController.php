<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Email\EmailConfirmationSender;
use App\Entity\User;
use App\Form\ProfileUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/profile', name: 'profile_update')]
class UpdateController extends AbstractController
{
    public function __construct(
        private readonly EmailConfirmationSender $emailConfirmationSender,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $currentEmail = $user->getEmail();

        $form = $this->createForm(ProfileUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentEmail !== $user->getEmail()) {
                $user->unverifyEmail();
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            if ($currentEmail !== $user->getEmail()) {
                $this->emailConfirmationSender->send($user);

                $this->addFlash('info', 'Ton adresse mail a été modifiée, merci de la valider grâce au mail qui vient de t\'être envoyé.');
            } else {
                $this->addFlash('success', 'Ton profil a bien été mis à jour !');
            }

            return $this->redirectToRoute('profile_update');
        }

        return $this->render('profile/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
