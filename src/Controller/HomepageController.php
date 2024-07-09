<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'homepage')]
class HomepageController extends AbstractController
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

        return $this->render('homepage.html.twig', [
            'events' => $qb->getQuery()->getResult(),
        ]);
    }
}
