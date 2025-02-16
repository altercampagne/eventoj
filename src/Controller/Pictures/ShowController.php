<?php

declare(strict_types=1);

namespace App\Controller\Pictures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/pictures', name: 'pictures_show')]
class ShowController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('e, s, p')
            ->from(Event::class, 'e')
            ->leftJoin('e.stages', 's')
            ->leftJoin('e.picture', 'p')
            ->where('e.publishedAt < :now')
            ->andWhere('s.date < :now')
            ->orderBy('s.date', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
        ;

        return $this->render('pictures/show.html.twig', [
            'events' => $qb->getQuery()->getResult(),
        ]);
    }
}
