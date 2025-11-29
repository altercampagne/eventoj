<?php

declare(strict_types=1);

namespace App\Admin\Controller\Payment;

use App\Admin\Security\Permission;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::PAYMENT_LIST->value)]
#[Route('/_admin/payments/{status}', name: 'admin_payment_list', requirements: ['status' => new EnumRequirement(PaymentStatus::class)])]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, ?PaymentStatus $status = null): Response
    {
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->getQueryBuilder($status)),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/payment/list.html.twig', [
            'pager' => $pager,
            'selectedStatus' => $status,
        ]);
    }

    private function getQueryBuilder(?PaymentStatus $status = null): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('p, m')
            ->from(Payment::class, 'p')
            ->leftJoin('p.payer', 'm')
            ->orderBy('p.createdAt', 'DESC')
        ;

        if (null !== $status) {
            $qb
                ->andWhere('p.status = :status')
                ->setParameter('status', $status)
            ;
        } else {
            $qb
                ->andWhere('p.status != :status')
                ->setParameter('status', PaymentStatus::EXPIRED)
            ;
        }

        return $qb;
    }
}
