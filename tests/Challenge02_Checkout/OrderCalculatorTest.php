<?php

namespace Tests\Challenge02_Checkout;

use App\Challenge02_Checkout\Refactored\Domain\CartItemVO;
use App\Challenge02_Checkout\Refactored\Domain\OrderCalculator;
use PHPUnit\Framework\TestCase;

class OrderCalculatorTest extends TestCase
{
    private OrderCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new OrderCalculator();
    }

    public function testCalculatesTotalWithoutDiscount(): void
    {
        $items = [
            new CartItemVO(1, 'Mouse', 2, 50.00),
            new CartItemVO(2, 'Teclado', 1, 100.00),
        ];

        // 2*50 + 1*100 = 200.00 — abaixo de R$1000, sem desconto
        $this->assertEquals(200.00, $this->calculator->calculateTotal($items));
    }

    public function testAppliesTenPercentDiscountWhenTotalExceedsOneThousand(): void
    {
        $items = [new CartItemVO(1, 'Notebook', 2, 600.00)];

        // 2*600 = 1200.00 — acima de R$1000, desconto de 10%
        $this->assertEquals(1080.00, $this->calculator->calculateTotal($items));
    }

    public function testNoDiscountWhenTotalIsExactlyOneThousand(): void
    {
        $items = [new CartItemVO(1, 'Monitor', 1, 1000.00)];

        // A regra é > 1000, não >= 1000 — limite exato não recebe desconto
        $this->assertEquals(1000.00, $this->calculator->calculateTotal($items));
    }

    public function testThrowsExceptionForEmptyCart(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O carrinho não pode estar vazio.');

        $this->calculator->calculateTotal([]);
    }
}
