<?php

namespace App\Challenge02_Checkout\Refactored\Processor;

use App\Challenge02_Checkout\Refactored\Domain\CartItemVO;
use App\Challenge02_Checkout\Refactored\Domain\CustomerVO;
use App\Challenge02_Checkout\Refactored\Domain\OrderCalculator;
use App\Challenge02_Checkout\Refactored\Interfaces\PaymentGatewayInterface;
use App\Challenge02_Checkout\Refactored\Repositories\OrderRepository;
use App\Challenge02_Checkout\Refactored\Services\InventoryService;
use App\Challenge02_Checkout\Refactored\Services\ReceiptGeneratorService;

class CheckoutProcessor {
    private InventoryService $inventory;
    private OrderCalculator $calculator;
    private PaymentGatewayInterface $paymentGateway;
    private OrderRepository $repository;
    private ReceiptGeneratorService $receiptGenerator;

    public function __construct(
        InventoryService $inventory,
        OrderCalculator $calculator,
        PaymentGatewayInterface $paymentGateway,
        OrderRepository $repository,
        ReceiptGeneratorService $receiptGenerator
    ) {
        $this->inventory = $inventory;
        $this->calculator = $calculator;
        $this->paymentGateway = $paymentGateway;
        $this->repository = $repository;
        $this->receiptGenerator = $receiptGenerator;
    }

    /**
     * @param CartItemVO[] $cartItems
     */
    public function processOrder(array $cartItems, CustomerVO $customer, string $creditCardToken): array {

        // 1. Verificar estoque
        $this->inventory->checkAvailability($cartItems);

        // 2. Calcular total e descontos
        $totalAmount = $this->calculator->calculateTotal($cartItems);

        // 3. Processar pagamento (throws RuntimeException on decline)
        $this->paymentGateway->charge($totalAmount, $creditCardToken, $customer->email);

        // 4. Salvar pedido
        $orderId = $this->repository->save($customer->email, $totalAmount);

        // 5. Gerar e salvar recibo
        $receipt = $this->receiptGenerator->generate($orderId, $totalAmount);
        $this->receiptGenerator->save($orderId, $receipt);

        return [
            'success'  => true,
            'order_id' => $orderId,
        ];
    }
}
