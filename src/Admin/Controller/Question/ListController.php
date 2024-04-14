<?php

declare(strict_types=1);

namespace App\Admin\Controller\Question;

use App\Admin\Security\Permission;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::QUESTION_LIST->value)]
#[Route('/questions', name: 'admin_question_list')]
class ListController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('admin/question/list.html.twig', [
            'questions' => $this->em->getRepository(Question::class)->findAll(),
        ]);
    }
}
