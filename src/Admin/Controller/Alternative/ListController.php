<?php

declare(strict_types=1);

namespace App\Admin\Controller\Alternative;

use App\Admin\Security\Permission;
use App\Entity\Alternative;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ALTERNATIVE_LIST->value)]
#[Route('/alternatives', name: 'admin_alternative_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->getQueryBuilder($request->query->get('event'))),
            (int) $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/alternative/list.html.twig', [
            'pager' => $pager,
            'events' => $this->getEvents(),
        ]);
    }

    private function getQueryBuilder(?string $event = null): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a, s, e')
            ->from(Alternative::class, 'a')
            ->leftJoin('a.stages', 's')
            ->leftJoin('s.event', 'e')
            ->orderBy('a.name', 'ASC')
        ;

        if (null !== $event) {
            $qb
                ->andWhere('e.slug = :event_slug')
                ->setParameter('event_slug', $event)
            ;
        }

        return $qb;
    }

    /**
     * @return Event[]
     */
    private function getEvents(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e, s, p')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('e.picture', 'p')
            ->orderBy('s.date', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
}
