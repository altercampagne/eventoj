<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_LIST->value)]
#[Route('/payments', name: 'admin_payment_list')]
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
            (int) $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/payment/list.html.twig', [
            'pager' => $pager,
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p, m')
            ->from(Payment::class, 'p')
            ->leftJoin('p.payer', 'm')
            ->orderBy('p.createdAt', 'DESC')
        ;

        return $qb;
    }
}
