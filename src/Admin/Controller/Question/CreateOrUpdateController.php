<?php

declare(strict_types=1);

namespace App\Admin\Controller\Question;

use App\Admin\Form\QuestionFormType;
use App\Admin\Security\Permission;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[IsGranted(Permission::QUESTION_CREATE->value)]
    #[Route('/questions/create', name: 'admin_question_create')]
    public function create(Request $request): Response
    {
        return $this->update($request, new Question(), true);
    }

    #[IsGranted(Permission::QUESTION_UPDATE->value, 'question')]
    #[Route('/questions/{slug}/update', name: 'admin_question_update')]
    public function update(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Question $question,
        bool $creation = false,
    ): Response {
        $form = $this->createForm(QuestionFormType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($question);
            $this->em->flush();

            $this->addFlash('success', \sprintf('La question a bien été %s ! 🥳', $creation ? 'créée' : 'modifiée'));

            return $this->redirectToRoute('admin_question_show', ['slug' => $question->getSlug()]);
        }

        return $this->render('admin/question/edit.html.twig', [
            'creation' => $creation,
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }
}
