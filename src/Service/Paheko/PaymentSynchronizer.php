<?php

declare(strict_types=1);

namespace App\Service\Paheko;

use App\Entity\Payment;
use App\Service\Paheko\Client\PahekoClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Payment are sync'ed in the Paheko's Helloasso account (512D).
 * It's ID depends on the environment.
 *
 * @see https://compta.altercampagne.net/admin/acc/accounts/
 */
final readonly class PaymentSynchronizer
{
    public function __construct(
        private PahekoClientInterface $pahekoClient,
        private UserSynchronizer $userSynchronizer,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger,
        private string $pahekoHelloassoAccountCode,
        private int $pahekoMembershipsProjectId,
    ) {
    }

    public function sync(Payment $payment): void
    {
        if (!$this->canBeSynced($payment)) {
            return;
        }

        $this->userSynchronizer->sync($payment->getRegistration()->getUser());

        if (null !== $id = $payment->getPahekoId()) {
            $this->pahekoClient->updatePayment($id, $this->getPaymentData($payment));

            return;
        }

        $pahekoPayment = $this->pahekoClient->createPayment($this->getPaymentData($payment));
        /* @phpstan-ignore-next-line */
        $id = (string) $pahekoPayment['id'];

        $payment->setPahekoId($id);

        $this->em->persist($payment);
        $this->em->flush();
    }

    public function canBeSynced(Payment $payment): bool
    {
        if (!$payment->isApproved()) {
            return false;
        }

        $registration = $payment->getRegistration();

        if (!$registration->isConfirmed()) {
            $this->logger->warning('Cannot sync a non-confirmed registration on Paheko.', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function getPaymentData(Payment $payment): array
    {
        $event = $payment->getRegistration()->getEvent();

        $label = $payment->getRegistration()->countPeople() > 1 ? 'Inscriptions' : 'Inscription';

        $lines = [
            [
                'account' => 706, // Prestation de services
                'credit' => $payment->getRegistrationOnlyAmount() / 100,
                'debit' => 0,
                'label' => $label,
                'id_project' => $event->getPahekoProjectId(),
            ],
            [
                'account' => $this->pahekoHelloassoAccountCode,
                'credit' => 0,
                'debit' => $payment->getRegistrationOnlyAmount() / 100,
                'label' => $label,
                'id_project' => $event->getPahekoProjectId(),
            ],
        ];

        if (0 < $payment->getMembershipsAmount()) {
            $label = 1 < \count($payment->getMemberships()) ? 'Adhésions' : 'Adhésion';

            $lines[] = [
                'account' => 756, // Cotisations
                'credit' => $payment->getMembershipsAmount() / 100,
                'debit' => 0,
                'label' => $label,
                'id_project' => $this->pahekoMembershipsProjectId,
            ];
            $lines[] = [
                'account' => $this->pahekoHelloassoAccountCode,
                'credit' => 0,
                'debit' => $payment->getMembershipsAmount() / 100,
                'label' => $label,
                'id_project' => $this->pahekoMembershipsProjectId,
            ];
        }

        $paymentAdminUrl = $this->urlGenerator->generate('admin_payment_show', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return [
            'id_year' => 'current',
            /* @phpstan-ignore-next-line */
            'date' => $payment->getApprovedAt()->format('Y-m-d'),
            'label' => "{$payment->getPayer()->getFullname()} pour {$event->getName()}",
            'type' => 'ADVANCED',
            'lines' => $lines,
            'linked_users' => [$payment->getPayer()->getPahekoId()],
            'notes' => "Paiement visible ici : $paymentAdminUrl",
            'reference' => (string) $payment->getId(),
        ];
    }
}
