<?php

declare(strict_types=1);

namespace App\Service\Paheko;

use App\Entity\Payment;
use App\Service\Paheko\Client\PahekoClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\ClientException;
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
        #[Autowire(env: 'PAHEKO_HELLOASSO_ACCOUNT_CODE')]
        private string $pahekoHelloassoAccountCode,
        #[Autowire(env: 'PAHEKO_MEMBERSHIPS_PROJECT_ID')]
        private int $pahekoMembershipsProjectId,
        #[Autowire(param: 'kernel.environment')]
        private string $environment,
    ) {
    }

    public function sync(Payment $payment): void
    {
        if (!$this->canBeSynced($payment)) {
            return;
        }

        try {
            $this->userSynchronizer->sync($payment->getPayer());
            $this->syncPayment($payment);
            if ($payment->isRefunded()) {
                $this->syncRefund($payment);
            }
        } catch (\Exception $e) {
            // In dev env we silently ignore this error
            if ('dev' === $this->environment) {
                $this->logger->warning("En error occurred when synchronising a payment. This error have been ignored in dev env but it won't be in production.", [
                    'payment' => $payment,
                    'exception' => $e,
                ]);

                return;
            }

            throw $e;
        }
    }

    public function canBeSynced(Payment $payment): bool
    {
        if (!$payment->isApproved() && !$payment->isRefunded()) {
            return false;
        }

        if (0 === $payment->getAmount()) {
            return false;
        }

        if (null === $registration = $payment->getRegistration()) {
            return true;
        }

        if (!$registration->isConfirmed() && !$registration->isCanceled()) {
            $this->logger->warning('Cannot sync a non-confirmed or canceled registration on Paheko.', [
                'registration_id' => (string) $registration->getId(),
            ]);

            return false;
        }

        return true;
    }

    private function syncPayment(Payment $payment): void
    {
        if (null === $payment->getApprovedAt()) {
            return;
        }

        if (null !== $id = $payment->getPahekoPaymentId()) {
            $this->pahekoClient->updatePayment($id, $this->getPaymentData($payment));

            return;
        }

        try {
            $pahekoPayment = $this->pahekoClient->createPayment($this->getPaymentData($payment));
        } catch (ClientException $e) {
            $this->logger->error('Fail to create payment on Paheko', [
                'payment' => $payment,
                'response' => $e->getResponse()->toArray(false),
            ]);

            throw $e;
        }

        /* @phpstan-ignore-next-line */
        $id = (string) $pahekoPayment['id'];

        $payment->setPahekoPaymentId($id);

        $this->em->persist($payment);
        $this->em->flush();
    }

    private function syncRefund(Payment $payment): void
    {
        if (null !== $id = $payment->getPahekoRefundId()) {
            $this->pahekoClient->updatePayment($id, $this->getRefundData($payment));

            return;
        }

        $pahekoPayment = $this->pahekoClient->createPayment($this->getRefundData($payment));
        /* @phpstan-ignore-next-line */
        $id = (string) $pahekoPayment['id'];

        $payment->setPahekoRefundId($id);

        $this->em->persist($payment);
        $this->em->flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function getPaymentData(Payment $payment): array
    {
        $lines = [];
        if (null !== $registration = $payment->getRegistration()) {
            $event = $registration->getEvent();
            $mainLabel = "{$payment->getPayer()->getFullname()} pour {$event->getName()}";
            $label = $registration->countPeople() > 1 ? 'Inscriptions' : 'Inscription';

            // This can happen if someone register for before or after and pay its membership.
            // We have a payment linked to a registration with a null registration only amount
            if (0 < $payment->getRegistrationOnlyAmount()) {
                $lines[] = [
                    'account' => 706, // Prestation de services
                    'credit' => $payment->getRegistrationOnlyAmount() / 100,
                    'debit' => 0,
                    'label' => $label,
                    'id_project' => $event->getPahekoProjectId(),
                ];
                $lines[] = [
                    'account' => $this->pahekoHelloassoAccountCode,
                    'credit' => 0,
                    'debit' => $payment->getRegistrationOnlyAmount() / 100,
                    'label' => $label,
                    'id_project' => $event->getPahekoProjectId(),
                ];
            }
        } else {
            $mainLabel = "{$payment->getPayer()->getFullname()} pour une adhésion";
        }

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

        if ([] === $lines) {
            throw new \RuntimeException('Given payment contains no registration nor membership...');
        }

        $paymentAdminUrl = $this->urlGenerator->generate('admin_payment_show', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        if (null == $approvedAt = $payment->getApprovedAt()) {
            throw new \RuntimeException('Unable to sync a payment which has never been approved.');
        }

        return [
            'id_year' => $this->getIdYear($approvedAt),
            'date' => $payment->getApprovedAt()->format('Y-m-d'),
            'label' => $mainLabel,
            'type' => 'ADVANCED',
            'lines' => $lines,
            'linked_users' => [$payment->getPayer()->getPahekoId()],
            'notes' => "Paiement visible ici : {$paymentAdminUrl}",
            'reference' => (string) $payment->getId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getRefundData(Payment $payment): array
    {
        $lines = [];
        $mainLabel = "Remboursement {$payment->getPayer()->getFullname()}";

        if (null !== $registration = $payment->getRegistration()) {
            $event = $registration->getEvent();

            $mainLabel .= " pour {$event->getName()}";
            $label = 'Remboursement ';
            $label .= $registration->countPeople() > 1 ? 'inscriptions' : 'inscription';

            // This can happen if someone register for before or after and pay its membership.
            // We have a payment linked to a registration with a null registration only amount
            if (0 < $payment->getRegistrationOnlyAmount()) {
                $lines[] = [
                    'account' => 706, // Prestation de services
                    'credit' => 0,
                    'debit' => $payment->getRegistrationOnlyAmount() / 100,
                    'label' => $label,
                    'id_project' => $event->getPahekoProjectId(),
                ];
                $lines[] = [
                    'account' => $this->pahekoHelloassoAccountCode,
                    'credit' => $payment->getRegistrationOnlyAmount() / 100,
                    'debit' => 0,
                    'label' => $label,
                    'id_project' => $event->getPahekoProjectId(),
                ];
            }
        }

        if (0 < $payment->getMembershipsAmount() && $payment->isFullyRefunded()) {
            $label = 1 < \count($payment->getMemberships()) ? 'Adhésions' : 'Adhésion';

            $lines[] = [
                'account' => 756, // Cotisations
                'credit' => 0,
                'debit' => $payment->getMembershipsAmount() / 100,
                'label' => $label,
                'id_project' => $this->pahekoMembershipsProjectId,
            ];
            $lines[] = [
                'account' => $this->pahekoHelloassoAccountCode,
                'credit' => $payment->getMembershipsAmount() / 100,
                'debit' => 0,
                'label' => $label,
                'id_project' => $this->pahekoMembershipsProjectId,
            ];
        }

        $paymentAdminUrl = $this->urlGenerator->generate('admin_payment_show', ['id' => (string) $payment->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        if (null == $approvedAt = $payment->getApprovedAt()) {
            throw new \RuntimeException('Unable to sync a payment which has never been approved.');
        }

        return [
            'id_year' => $this->getIdYear($approvedAt),
            'date' => $payment->getApprovedAt()->format('Y-m-d'),
            'label' => $mainLabel,
            'type' => 'ADVANCED',
            'lines' => $lines,
            'linked_users' => [$payment->getPayer()->getPahekoId()],
            'notes' => "Paiement visible ici : {$paymentAdminUrl}",
            'reference' => (string) $payment->getId(),
            'linked_transactions' => [$payment->getPahekoPaymentId()],
        ];
    }

    /**
     * @see https://compta.altercampagne.net/admin/acc/years/
     */
    private function getIdYear(\DateTimeImmutable $date): int
    {
        if ($date < new \DateTimeImmutable('2023-10-01')) {
            throw new \RuntimeException('Cannot sync a payment which is before 2023-10-01.');
        }

        $year = 2024;
        $yearId = 2;

        while (true) {
            if ($date < new \DateTimeImmutable("{$year}-10-01")) {
                return $yearId;
            }

            ++$year;
            ++$yearId;
        }
    }
}
