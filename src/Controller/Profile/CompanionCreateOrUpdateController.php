<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Entity\Companion;
use App\Entity\User;
use App\Form\CompanionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/companions/create/{backToEvent}', name: 'profile_companion_create')]
#[Route('/me/companions/{id}/{backToEvent}', name: 'profile_companion_update', requirements: ['id' => Requirement::UUID])]
class CompanionCreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, ?Companion $companion, ?string $backToEvent = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null === $companion) {
            $companion = new Companion($user);
            $creation = true;
        }

        $form = $this->createForm(CompanionFormType::class, $companion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($companion);
            $this->em->flush();

            if ($creation ?? false) {
                $this->addFlash('success', "{$companion->getFullName()} a bien été ajouté·e !");
            } else {
                $this->addFlash('success', "{$companion->getFullName()} a bien été modifié·e !");
            }

            if (null !== $backToEvent) {
                return $this->redirectToRoute('event_register', ['slug' => $backToEvent]);
            }

            return $this->redirectToRoute('profile_companions');
        }

        return $this->render('profile/companion_form.html.twig', [
            'form' => $form->createView(),
            'creation' => $creation ?? false,
        ]);
    }
}
