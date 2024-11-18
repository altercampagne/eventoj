<?php

declare(strict_types=1);

namespace App\Controller\Alternative;

use App\Entity\Alternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/alternatives', name: 'alternative_map')]
class MapController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('a, s, e')
            ->from(Alternative::class, 'a')
            ->leftJoin('a.stages', 's')
            ->leftJoin('s.event', 'e')
            ->andWhere('e.publishedAt is not null')
        ;

        $alternatives = $qb->getQuery()->getResult();

        return $this->render('alternative/map.html.twig', [
            'alternatives' => $alternatives,
            'alternativeCountByDepartments' => $this->countAlternativesByDepartments($alternatives),
        ]);
    }

    /**
     * @param Alternative[] $alternatives
     *
     * @return array<int, int>
     */
    private function countAlternativesByDepartments(array $alternatives): array
    {
        $departments = array_fill(1, 95, 0);
        foreach ($alternatives as $alternative) {
            if (null === $department = $alternative->getAddress()->getDepartment()) {
                continue;
            }

            ++$departments[$department];
        }

        return $departments;
    }
}
