<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use App\Entity\Alternative;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ADMIN_ACCESS->value)]
#[Route('/_admin/', name: 'admin')]
class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(?string $slug = null): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'events' => $this->getComingEvents(),
            'alternatives' => $this->getAlternativesToImprove(),
        ]);
    }

    /**
     * @return Event[]
     */
    private function getComingEvents(): array
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

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Alternative[]
     */
    private function getAlternativesToImprove(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a, p')
            ->from(Alternative::class, 'a')
            ->leftJoin('a.pictures', 'p')
            ->where('p.id IS NULL')
            ->orderBy('a.updatedAt', 'ASC')
            ->setMaxResults(10)
        ;

        return $qb->getQuery()->getResult();
    }
}
