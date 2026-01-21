<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Service\Payment\Instalment;

use App\Factory\PaymentFactory;
use App\Service\Payment\Instalment\Instalment;
use App\Service\Payment\Instalment\TermsCalculator;
use Helloasso\Models\Carts\InitCheckoutBody;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class TermsCalculatorTest extends TestCase
{
    use Factories;

    public function testCalculateWithBasicParameters(): void
    {
        $instalments = new TermsCalculator()->calculate(1000, 3, firstPaymentDate: new \DateTimeImmutable('2023-05-05'));

        self::assertEquals([
            new Instalment(new \DateTimeImmutable('2023-05-05'), 334),
            new Instalment(new \DateTimeImmutable('2023-06-05'), 333),
            new Instalment(new \DateTimeImmutable('2023-07-05'), 333),
        ], $instalments);
    }

    public function testCalculateWithDateAfter27(): void
    {
        $instalments = new TermsCalculator()->calculate(1000, 3, firstPaymentDate: new \DateTimeImmutable('2023-05-30'));

        self::assertEquals([
            new Instalment(new \DateTimeImmutable('2023-05-30'), 334),
            new Instalment(new \DateTimeImmutable('2023-06-27'), 333),
            new Instalment(new \DateTimeImmutable('2023-07-27'), 333),
        ], $instalments);
    }

    public function testUpdateCheckoutBody(): void
    {
        $initCheckoutBody = new InitCheckoutBody();

        $payment = PaymentFactory::new()->withInstalments()->create(['amount' => 1000]);

        new TermsCalculator()->updateCheckoutBody($payment, $initCheckoutBody, new \DateTimeImmutable('2023-05-30'));

        self::assertSame(334, $initCheckoutBody->getInitialAmount());
        self::assertCount(2, $initCheckoutBody->getTerms());

        $firstTerm = $initCheckoutBody->getTerms()[0];
        self::assertSame(333, $firstTerm->getAmount());
        self::assertEquals(new \DateTimeImmutable('2023-06-27'), $firstTerm->getDate());
        $secondTerm = $initCheckoutBody->getTerms()[1];
        self::assertSame(333, $secondTerm->getAmount());
        self::assertEquals(new \DateTimeImmutable('2023-07-27'), $secondTerm->getDate());
    }
}
