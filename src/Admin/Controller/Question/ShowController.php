<?php

declare(strict_types=1);

namespace App\Admin\Controller\Question;

use App\Admin\Security\Permission;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::QUESTION_VIEW->value, 'question')]
#[Route('/questions/{slug}', name: 'admin_question_show')]
class ShowController extends AbstractController
{
    public function __invoke(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Question $question,
    ): Response {
        return $this->render('admin/question/show.html.twig', [
            'question' => $question,
        ]);
    }
}
