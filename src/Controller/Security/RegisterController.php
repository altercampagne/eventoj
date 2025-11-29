<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Email\EmailConfirmationSender;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Message\GeocodeUserAddressMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route('/register', name: 'register')]
class RegisterController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly EmailConfirmationSender $emailConfirmationSender,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('profile_homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->bus->dispatch(new GeocodeUserAddressMessage($user->getId()));

            $this->emailConfirmationSender->send($user);

            // Target path must be used before connected user, because once logged the session have been changed.
            if (null !== $targetUrl = $this->getTargetPath($request->getSession(), 'main')) {
                $this->removeTargetPath($request->getSession(), 'main');
            }

            $this->security->login($user, 'form_login');

            if (null !== $targetUrl) {
                $this->saveTargetPath($request->getSession(), 'main', $targetUrl);
            }

            return $this->redirectToRoute('profile_update_profile');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
