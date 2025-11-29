<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\User;
use App\Service\Paheko\UserSynchronizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::USER_SYNC_WITH_PAHEKO->value, 'user')]
#[Route('/_admin/users/{id}/sync_with_paheko', name: 'admin_user_sync_with_paheko', requirements: ['id' => Requirement::UUID], methods: 'POST')]
class SyncWithPahekoController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly UserSynchronizer $userSynchronizer,
    ) {
    }

    public function __invoke(User $user): Response
    {
        $this->userSynchronizer->sync($user);

        $this->addFlash('success', "{$user->getFullName()} a bien été synchronisé·e avec Paheko.");

        return $this->redirectToRefererOrToRoute('admin_user_show', ['id' => $user->getId()]);
    }
}
