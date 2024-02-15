<?php

declare(strict_types=1);

namespace App\Controller\Admin\Alternative;

use App\Entity\Alternative;
use App\Form\Admin\AlternativeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/_admin/alternatives/create', name: 'admin_alternative_create')]
#[Route('/_admin/alternatives/{slug}/update', name: 'admin_alternative_update')]
final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, ?Alternative $alternative = null): Response
    {
        $creation = false;
        if (null === $alternative) {
            $creation = true;
            $alternative = new Alternative();
        }

        $form = $this->createForm(AlternativeFormType::class, $alternative);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($alternative);
            $this->em->flush();

            $this->addFlash('success', sprintf('L\'alternative a bien été %s ! 🥳', $creation ? 'créée' : 'modifiée'));

            return $this->redirectToRoute('admin_alternative_list');
        }

        return $this->render('admin/alternative/edit.html.twig', [
            'creation' => $creation,
            'alternative' => $alternative,
            'form' => $form->createView(),
        ]);
    }
}
