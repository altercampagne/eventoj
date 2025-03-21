<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\Membership;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * This endpoint is used to create a membership not linked to a payment. Useful
 * to "offer" a membership to someone or to create a membership paid by cash.
 */
#[IsGranted(Permission::USER_MEMBERSHIP_CREATE->value, 'user')]
#[Route('/users/{id}/membership_create', name: 'admin_user_membership_create', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class MembershipCreateController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, User $user): Response
    {
        if (!$user->isMember()) {
            $membership = Membership::createForUser($user);

            $this->em->persist($membership);
            $this->em->flush();

            $this->addFlash('success', "Adhésion créée avec succès pour {$user->getFullName()} !");

            return $this->redirectToRefererOrToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        /** @var Membership */
        $latestMembership = $user->getLatestMembership();

        if ($latestMembership->isValidAt(new \DateTimeImmutable('+1 year'))) {
            $this->addFlash('danger', "{$user->getFullName()} est encore membre pour plus d'un an, impossible de prolonger son adhésion.");

            return $this->redirectToRefererOrToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        $membership = Membership::createForUser($user, startAt: $latestMembership->getEndAt()->modify('+1 day'));

        $this->em->persist($membership);
        $this->em->flush();

        $this->addFlash('success', "Adhésion prolongée avec succès pour {$user->getFullName()} !");

        return $this->redirectToRefererOrToRoute('admin_user_show', ['id' => $user->getId()]);
    }
}
