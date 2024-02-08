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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/me/companions/create', name: 'profile_companion_create')]
#[Route('/me/companions/{id}', name: 'profile_companion_update')]
class CompanionCreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, ?Companion $companion): Response
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

            $this->addFlash('success', "{$companion->getFullName()} est à jour !");

            return $this->redirectToRoute('profile_companions');
        }

        return $this->render('profile/companion_form.html.twig', [
            'form' => $form->createView(),
            'creation' => $creation ?? false,
        ]);
    }
}
