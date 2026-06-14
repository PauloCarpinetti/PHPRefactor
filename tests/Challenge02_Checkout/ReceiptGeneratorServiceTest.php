<?php

namespace Tests\Challenge02_Checkout;

use App\Challenge02_Checkout\Refactored\Services\ReceiptGeneratorService;
use PHPUnit\Framework\TestCase;

class ReceiptGeneratorServiceTest extends TestCase
{
    private ReceiptGeneratorService $service;

    protected function setUp(): void
    {
        $this->service = new ReceiptGeneratorService();
    }

    public function testGenerateReturnsStringContainingOrderIdAndTotal(): void
    {
        $receipt = $this->service->generate(99, 450.00);

        $this->assertIsString($receipt);
        $this->assertStringContainsString('99', $receipt);
        $this->assertStringContainsString('450', $receipt);
    }

    public function testSaveThrowsExceptionWhenDirectoryDoesNotExist(): void
    {
        $service = new ReceiptGeneratorService('/caminho/invalido/que/nao/existe/');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Não foi possível salvar o recibo do pedido #1.');

        $service->save(1, 'conteúdo do recibo');
    }
}
