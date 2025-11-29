<?php

declare(strict_types=1);

namespace App\Admin\Controller\Membership;

use App\Admin\Security\Permission;
use App\Entity\Membership;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::MEMBERSHIP_LIST->value)]
#[Route('/_admin/memberships', name: 'admin_membership_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->getQueryBuilder()),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/membership/list.html.twig', [
            'pager' => $pager,
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('m, u, c, cu')
            ->from(Membership::class, 'm')
            ->leftJoin('m.user', 'u')
            ->leftJoin('m.companion', 'c')
            ->leftJoin('c.user', 'cu')
            ->andWhere('m.endAt >= :now')
            ->andWhere('m.canceledAt is null')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('m.createdAt', 'DESC')
        ;

        return $qb;
    }
}
