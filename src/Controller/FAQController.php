<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/faq', name: 'faq')]
class FAQController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('misc/faq.html.twig', [
            'questions' => $this->em->getRepository(Question::class)->findAll(),
        ]);
    }
}
