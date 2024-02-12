<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/users/{id}/unpromote', name: 'admin_user_unpromote_admin', requirements: ['id' => Requirement::UUID_V4])]
class UnpromoteAdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, User $user): Response
    {
        $user->setRoles([]);

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', "{$user->getFullName()} n'est plus admin!");

        if (null !== $targetUrl = $request->headers->get('Referer')) {
            return $this->redirect($targetUrl);
        }

        return $this->redirectToRoute('admin_user_list');
    }
}
