<?php

declare(strict_types=1);

namespace App\Controller\Admin\Registration;

use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/registrations', name: 'admin_registration_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('admin/registration/list.html.twig', [
            'registrations' => $this->em->getRepository(Registration::class)->findAll(),
        ]);
    }
}
