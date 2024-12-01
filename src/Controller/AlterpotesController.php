<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/alterpotes', name: 'alterpotes_map')]
class AlterpotesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isMember()) {
            $this->addFlash('danger', 'Une adhésion à jour est nécessaire pour accéder à la carte des alterpotes !');

            return $this->redirectToRoute('profile_memberships');
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.visibleOnAlterpotesMap = true')
            ->andWhere('u.address.latitude IS NOT NULL')
            ->andWhere('u.address.longitude IS NOT NULL')
        ;

        $users = $qb->getQuery()->getResult();

        return $this->render('alterpotes.html.twig', [
            'users' => $users,
        ]);
    }
}
