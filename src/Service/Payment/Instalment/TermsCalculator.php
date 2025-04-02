<?php

declare(strict_types=1);

namespace App\Service\Payment\Instalment;

use App\Entity\Payment;
use Helloasso\Models\Carts\CheckoutTerm;
use Helloasso\Models\Carts\InitCheckoutBody;

final class TermsCalculator
{
    /**
     * @return Instalment[]
     */
    public function calculate(int $amount, int $instalmentCount = 3, ?\DateTimeImmutable $firstPaymentDate = null): array
    {
        $firstPaymentDate ??= new \DateTimeImmutable();

        $instalmentAmount = (int) round($amount / $instalmentCount);
        $initialAmount = $amount - ($instalmentCount - 1) * $instalmentAmount;

        $instalments = [new Instalment($firstPaymentDate, $initialAmount)];

        $instalmentDate = $firstPaymentDate->modify('+1 month');
        if ((int) $instalmentDate->format('d') > 27) {
            $instalmentDate = $instalmentDate->setDate((int) $instalmentDate->format('Y'), (int) $instalmentDate->format('m'), 27);
        }

        for ($i = 1; $i < $instalmentCount; ++$i) {
            $instalments[] = new Instalment($instalmentDate, $instalmentAmount);
            $instalmentDate = $instalmentDate->modify('+1 month');
        }

        return $instalments;
    }

    /**
     * @param ?\DateTimeImmutable $atDate this param is used only in tests in order to simulate updates at a given date
     */
    public function updateCheckoutBody(Payment $payment, InitCheckoutBody $initCheckoutBody, ?\DateTimeImmutable $atDate = null): void
    {
        if (1 >= $payment->getInstalments()) {
            throw new \RuntimeException('Instalments must be more than 1.');
        }

        $instalments = $this->calculate($payment->getAmount(), $payment->getInstalments(), $atDate);

        $initCheckoutBody->setInitialAmount($instalments[0]->amount);

        array_shift($instalments);
        foreach ($instalments as $instalment) {
            $term = new CheckoutTerm();
            $term->setDate($instalment->date);
            $term->setAmount($instalment->amount);

            $initCheckoutBody->addTerm($term);
        }
    }
}
