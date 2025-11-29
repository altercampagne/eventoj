<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Admin\Security\Permission;
use App\Entity\Event;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::REGISTRATION_LIST->value)]
#[Route('/registrations', name: 'admin_registration_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->getQueryBuilder($request)),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/registration/list.html.twig', [
            'pager' => $pager,
        ]);
    }

    private function getQueryBuilder(Request $request): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('r, u, e, sr')
            ->from(Registration::class, 'r')
            ->leftJoin('r.user', 'u')
            ->leftJoin('r.event', 'e')
            ->leftJoin('r.stagesRegistrations', 'sr')
            ->orderBy('r.createdAt', 'DESC')
        ;

        if (null !== $slug = $request->query->get('event')) {
            if (null == $event = $this->em->getRepository(Event::class)->findOneBySlug($slug)) {
                throw $this->createNotFoundException("No event {$slug} found.");
            }

            $qb
                ->andWhere('r.event = :event')
                ->setParameter('event', $event)
            ;
        }

        return $qb;
    }
}
