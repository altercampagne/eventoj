<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Entity\User;
use App\Form\ProfileUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[IsGranted('ROLE_USER')]
#[Route('/me/profile', name: 'profile_update_profile')]
class UpdateProfileController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Ton profil a bien été mis à jour !');

            if (null !== $targetUrl = $this->getTargetPath($request->getSession(), 'main')) {
                $this->removeTargetPath($request->getSession(), 'main');
            }

            return $targetUrl ? $this->redirect($targetUrl) : $this->redirectToRoute('profile_update_profile');
        }

        return $this->render('profile/update_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
