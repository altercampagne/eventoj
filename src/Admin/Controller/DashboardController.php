<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ADMIN_ACCESS->value)]
#[Route('/', name: 'admin')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e, s, p')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('e.picture', 'p')
            ->where('s.date >= :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;

        return $this->render('admin/dashboard.html.twig', [
            'events' => $qb->getQuery()->getResult(),
        ]);
    }
}
