<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact', name: 'contact')]
class ContactController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('misc/contact.html.twig', [
            'questionsByCategory' => $this->em->getRepository(Question::class)->findAllGroupedByCategories(),
        ]);
    }
}
