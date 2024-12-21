<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::USER_VERIFY_EMAIL->value, 'user')]
#[Route('/users/{id}/verify_email', name: 'admin_user_verify_email', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class VerifyEmailController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, User $user): Response
    {
        $user->verifyEmail();

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', "L'adresse mail de {$user->getFullName()} a bien été validée !");

        return $this->redirectToRefererOrToRoute('admin_user_show', ['id' => $user->getId()]);
    }
}
