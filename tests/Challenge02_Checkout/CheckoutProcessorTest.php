<?php

namespace Tests\Challenge02_Checkout;

use App\Challenge02_Checkout\Refactored\Domain\CartItemVO;
use App\Challenge02_Checkout\Refactored\Domain\CustomerVO;
use App\Challenge02_Checkout\Refactored\Domain\OrderCalculator;
use App\Challenge02_Checkout\Refactored\Interfaces\PaymentGatewayInterface;
use App\Challenge02_Checkout\Refactored\Processor\CheckoutProcessor;
use App\Challenge02_Checkout\Refactored\Repositories\OrderRepository;
use App\Challenge02_Checkout\Refactored\Services\InventoryService;
use App\Challenge02_Checkout\Refactored\Services\ReceiptGeneratorService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CheckoutProcessorTest extends TestCase
{
    private $inventoryMock;
    private $calculatorMock;
    private $paymentGatewayMock;
    private $repositoryMock;
    private $receiptGeneratorMock;
    private CheckoutProcessor $processor;
    private array $cartItems;
    private CustomerVO $customer;

    protected function setUp(): void
    {
        $this->inventoryMock        = $this->createMock(InventoryService::class);
        $this->calculatorMock       = $this->createMock(OrderCalculator::class);
        $this->paymentGatewayMock   = $this->createMock(PaymentGatewayInterface::class);
        $this->repositoryMock       = $this->createMock(OrderRepository::class);
        $this->receiptGeneratorMock = $this->createMock(ReceiptGeneratorService::class);

        $this->processor = new CheckoutProcessor(
            $this->inventoryMock,
            $this->calculatorMock,
            $this->paymentGatewayMock,
            $this->repositoryMock,
            $this->receiptGeneratorMock
        );

        $this->cartItems = [new CartItemVO(1, 'Notebook', 2, 500.00)];
        $this->customer  = new CustomerVO('cliente@teste.com');
    }

    public function testProcessOrderSuccessfully(): void
    {
        $this->inventoryMock->expects($this->once())
            ->method('checkAvailability')
            ->with($this->cartItems);

        $this->calculatorMock->expects($this->once())
            ->method('calculateTotal')
            ->with($this->cartItems)
            ->willReturn(900.00);

        $this->paymentGatewayMock->expects($this->once())
            ->method('charge')
            ->with(900.00, 'token-123', 'cliente@teste.com');

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with('cliente@teste.com', 900.00)
            ->willReturn(42);

        $receiptContent = 'Receipt for order 42 with total amount 900';

        $this->receiptGeneratorMock->expects($this->once())
            ->method('generate')
            ->with(42, 900.00)
            ->willReturn($receiptContent);

        $this->receiptGeneratorMock->expects($this->once())
            ->method('save')
            ->with(42, $receiptContent);

        $result = $this->processor->processOrder($this->cartItems, $this->customer, 'token-123');

        $this->assertEquals(['success' => true, 'order_id' => 42], $result);
    }

    public function testThrowsExceptionWhenOutOfStock(): void
    {
        $this->inventoryMock->expects($this->once())
            ->method('checkAvailability')
            ->willThrowException(new RuntimeException('Produto Notebook sem estoque.'));

        // Nenhum passo posterior deve ser executado
        $this->calculatorMock->expects($this->never())->method('calculateTotal');
        $this->paymentGatewayMock->expects($this->never())->method('charge');
        $this->repositoryMock->expects($this->never())->method('save');
        $this->receiptGeneratorMock->expects($this->never())->method('generate');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Produto Notebook sem estoque.');

        $this->processor->processOrder($this->cartItems, $this->customer, 'token-123');
    }

    public function testThrowsExceptionWhenPaymentIsDeclined(): void
    {
        $this->inventoryMock->method('checkAvailability');

        $this->calculatorMock->method('calculateTotal')->willReturn(900.00);

        $this->paymentGatewayMock->expects($this->once())
            ->method('charge')
            ->willThrowException(new RuntimeException('Pagamento recusado.'));

        // O pedido não deve ser salvo se o pagamento falhar
        $this->repositoryMock->expects($this->never())->method('save');
        $this->receiptGeneratorMock->expects($this->never())->method('generate');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pagamento recusado.');

        $this->processor->processOrder($this->cartItems, $this->customer, 'token-invalido');
    }

    public function testReceiptIsNotGeneratedWhenOrderSaveFails(): void
    {
        $this->inventoryMock->method('checkAvailability');
        $this->calculatorMock->method('calculateTotal')->willReturn(900.00);
        $this->paymentGatewayMock->method('charge');

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new RuntimeException('Falha ao salvar o pedido.'));

        // O recibo não deve ser gerado nem salvo se o pedido falhar
        $this->receiptGeneratorMock->expects($this->never())->method('generate');
        $this->receiptGeneratorMock->expects($this->never())->method('save');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Falha ao salvar o pedido.');

        $this->processor->processOrder($this->cartItems, $this->customer, 'token-123');
    }
}
