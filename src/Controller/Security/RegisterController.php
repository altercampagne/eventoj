<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Email\EmailConfirmationSender;
use App\Entity\Address;
use App\Entity\User;
use App\Form\RegistrationFormDTO;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register', name: 'register')]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly EmailConfirmationSender $emailConfirmationSender,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationFormDTO $dto */
            $dto = $form->getData();

            $user = new User(
                email: $dto->email,
                firstName: $dto->firstName,
                lastName: $dto->lastName,
                birthDate: $dto->birthDate,
                address: new Address(
                    addressLine1: $dto->addressLine1,
                    addressLine2: $dto->addressLine2,
                    zipCode: $dto->zipCode,
                    city: $dto->city,
                    countryCode: $dto->countryCode,
                ),
                phoneNumber: $dto->phoneNumber,
            );
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->emailConfirmationSender->send($user);

            return $this->redirectToRoute('registration_waiting_for_email_validation');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
