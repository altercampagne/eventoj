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

#[IsGranted(Permission::USER_PROMOTE->value, 'user')]
#[Route('/users/{id}/promote', name: 'admin_user_promote_admin', requirements: ['id' => Requirement::UUID_V4])]
class PromoteAdminController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, User $user): Response
    {
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', "{$user->getFullName()} est maintenant admin!");

        return $this->redirectToRefererOrToRoute('admin_user_list');
    }
}
