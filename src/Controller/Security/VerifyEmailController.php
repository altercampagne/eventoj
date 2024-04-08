<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[Route('/verify/email', name: 'security_verify_email')]
class VerifyEmailController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly TranslatorInterface $translator,
        private readonly Security $security,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if (null === $id = $request->query->get('id')) {
            return $this->redirectToRoute('register');
        }

        if (null === $user = $this->entityManager->getRepository(User::class)->find($id)) {
            return $this->redirectToRoute('register');
        }

        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), (string) $user->getId(), $user->getEmail());
            $user->verifyEmail();

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->security->login($user, 'form_login');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('homepage');
        }

        $this->addFlash('success', 'Ton adresse mail a bien été validée ! 🥳');

        return $this->redirectToRoute('homepage');
    }
}
