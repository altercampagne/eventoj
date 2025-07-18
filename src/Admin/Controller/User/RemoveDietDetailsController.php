<?php

declare(strict_types=1);

namespace App\Admin\Controller\User;

use App\Admin\Controller\Util\RedirectorTrait;
use App\Admin\Security\Permission;
use App\Entity\Companion;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(Permission::USER_REMOVE_DIET_DETAILS->value)]
#[Route('/users/{id}/remove_diet_details', name: 'admin_user_remove_diet_details', requirements: ['id' => Requirement::UUID_V4], methods: 'POST')]
class RemoveDietDetailsController extends AbstractController
{
    use RedirectorTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Request $request, string $id): Response
    {
        if (null === $person = $this->em->getRepository(User::class)->findOneById($id)) {
            if (null === $person = $this->em->getRepository(Companion::class)->findOneById($id)) {
                throw new NotFoundHttpException('Given ID is neither a user nor a companion.');
            }
        }

        $person->setDietDetails(null);

        $this->em->persist($person);
        $this->em->flush();

        $this->addFlash('success', "Le régime particulier de {$person->getFullName()} a bien été supprimé !");

        return $this->redirectToRefererOrToRoute('admin_user_list');
    }
}
