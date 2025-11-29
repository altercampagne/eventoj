<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Admin\Security\Permission;
use App\Admin\Service\SearchEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::ADMIN_ACCESS->value)]
#[Route('/search', name: 'magic_search')]
class MagicSearchController extends AbstractController
{
    public function __construct(
        private readonly SearchEngine $searchEngine,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        if (null === $query = $request->query->get('query')) {
            return $this->redirectToRoute('admin');
        }

        $result = $this->searchEngine->search($query);

        if (0 === $result->getNbResults()) {
            return $this->render('admin/search.html.twig', ['result' => $result]);
        }

        if (1 < $result->getNbResults()) {
            return $this->render('admin/search.html.twig', ['result' => $result]);
        }

        if (1 === \count($result->users)) {
            return $this->redirectToRoute('admin_user_show', ['id' => (string) $result->users[0]->getId()]);
        }

        if (1 === \count($result->companions)) {
            return $this->redirectToRoute('admin_user_show', ['id' => (string) $result->companions[0]->getUser()->getId()]);
        }

        if (1 === \count($result->events)) {
            return $this->redirectToRoute('admin_event_show', ['slug' => (string) $result->events[0]->getSlug()]);
        }

        return $this->render('admin/search.html.twig', ['result' => $result]);
    }
}
