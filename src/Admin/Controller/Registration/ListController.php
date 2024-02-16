<?php

declare(strict_types=1);

namespace App\Admin\Controller\Registration;

use App\Entity\Event;
use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/registrations', name: 'admin_registration_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if (null !== $slug = $request->query->get('event')) {
            if (null == $event = $this->em->getRepository(Event::class)->findOneBySlug($slug)) {
                throw $this->createNotFoundException("No event $slug found.");
            }

            $filters = ['event' => $event];
        }

        return $this->render('admin/registration/list.html.twig', [
            'registrations' => $this->em->getRepository(Registration::class)->findBy($filters ?? [], ['createdAt' => 'DESC']),
        ]);
    }
}
