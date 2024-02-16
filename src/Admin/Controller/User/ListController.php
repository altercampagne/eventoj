<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/users', name: 'admin_user_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        return $this->render('admin/user/list.html.twig', [
            'users' => $this->em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
