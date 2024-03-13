<?php

declare(strict_types=1);

namespace App\Admin\Controller\Alternative;

use App\Admin\Form\AlternativeFormType;
use App\Admin\Security\Permission;
use App\Entity\Alternative;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CreateOrUpdateController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[IsGranted(Permission::ALTERNATIVE_CREATE->value)]
    #[Route('/alternatives/create', name: 'admin_alternative_create')]
    public function create(Request $request): Response
    {
        return $this->update($request, new Alternative(), true);
    }

    #[IsGranted(Permission::ALTERNATIVE_UPDATE->value, 'alternative')]
    #[Route('/alternatives/{slug}/update/{backToAlternative}', name: 'admin_alternative_update')]
    public function update(Request $request, Alternative $alternative, bool $creation = false, bool $backToAlternative = false): Response
    {
        $form = $this->createForm(AlternativeFormType::class, $alternative);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($alternative);
            $this->em->flush();

            $this->addFlash('success', sprintf('L\'alternative a bien été %s ! 🥳', $creation ? 'créée' : 'modifiée'));

            if ($backToAlternative) {
                return $this->redirectToRoute('alternative_show', ['slug' => $alternative->getSlug()]);
            }

            return $this->redirectToRoute('admin_alternative_show', ['slug' => $alternative->getSlug()]);
        }

        return $this->render('admin/alternative/edit.html.twig', [
            'creation' => $creation,
            'alternative' => $alternative,
            'form' => $form->createView(),
        ]);
    }
}
