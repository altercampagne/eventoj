<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Admin\Security\Permission;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::USER_VIEW->value, 'user')]
#[Route('/_admin/users/{id}', name: 'admin_user_show', requirements: ['id' => Requirement::UUID])]
class ShowController extends AbstractController
{
    public function __invoke(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
