<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Entity\Membership;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\Payment\PaymentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/payment/initiate_membership_payment', name: 'payment_initiate_membership_payment', methods: 'POST')]
class InitiateMembershipPaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentHandler $paymentHandler,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var User $payer */
        $payer = $this->getUser();

        if ($payer->isMember()) {
            return $this->redirectToRoute('profile_memberships');
        }

        return $this->redirect($this->paymentHandler->initiatePayment(new Payment($payer, Membership::PRICE)));
    }
}
