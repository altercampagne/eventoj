<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event', name: 'event_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e, s, p')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('e.picture', 'p')
            ->where('e.publishedAt < :now')
            ->orderBy('s.date', 'ASC') // Must be ordered like this to ensure `getFirtStage` & `getLastStage` from Event entity work correctly
            ->setParameter('now', new \DateTimeImmutable())
        ;

        return $this->render('event/list.html.twig', [
            'events' => $qb->getQuery()->getResult(),
        ]);
    }
}
