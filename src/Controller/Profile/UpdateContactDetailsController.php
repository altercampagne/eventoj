<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Email\EmailConfirmationSender;
use App\Entity\User;
use App\Form\ContactDetailsUpdateFormType;
use App\Message\GeocodeUserAddressMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/details', name: 'profile_update_contact_details')]
class UpdateContactDetailsController extends AbstractController
{
    public function __construct(
        private readonly EmailConfirmationSender $emailConfirmationSender,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $currentEmail = $user->getEmail();

        $form = $this->createForm(ContactDetailsUpdateFormType::class, $user);
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
                $this->addFlash('success', 'Tes coordonnées ont bien été mises à jour !');
            }

            $this->bus->dispatch(new GeocodeUserAddressMessage($user->getId()));

            return $this->redirectToRoute('profile_update_contact_details');
        }

        return $this->render('profile/update_contact_details.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
