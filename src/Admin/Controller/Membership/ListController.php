<?php

declare(strict_types=1);

namespace App\Admin\Controller\Membership;

use App\Admin\Security\Permission;
use App\Entity\Membership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::MEMBERSHIP_LIST->value)]
#[Route('/memberships', name: 'admin_membership_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('m, u, c, cu')
            ->from(Membership::class, 'm')
            ->leftJoin('m.user', 'u')
            ->leftJoin('m.companion', 'c')
            ->leftJoin('c.user', 'cu')
            ->andWhere('m.endAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;

        return $this->render('admin/membership/list.html.twig', [
            'memberships' => $qb->getQuery()->getResult(),
        ]);
    }
}
