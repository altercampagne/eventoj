<?php

declare(strict_types=1);

namespace App\Admin\Controller\Question;

use App\Admin\Security\Permission;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::QUESTION_DELETE->value, 'question')]
#[Route('/questions/{slug}/delete', name: 'admin_question_delete', methods: 'POST')]
class DeleteController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Question $question,
    ): Response {
        if ($question->isLocked()) {
            $this->addFlash('error', 'La question est utilisée ailleurs sur le site et ne peut pas être supprimée !');
        }

        $this->em->remove($question);
        $this->em->flush();

        $this->addFlash('success', 'La question a été supprimée !');

        return $this->redirectToRoute('admin_question_list');
    }
}
