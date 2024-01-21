<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/users/{slug}', name: 'admin_user_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(string $slug = null): Response
    {
        if (null !== $slug) {
            if (null == $event = $this->em->getRepository(Event::class)->findOneBySlug($slug)) {
                throw $this->createNotFoundException("No event $slug found.");
            }

            $filters = ['event' => $event];
        }

        return $this->render('admin/user/list.html.twig', [
            'users' => $this->em->getRepository(User::class)->findBy($filters ?? [], ['createdAt' => 'DESC']),
        ]);
    }
}