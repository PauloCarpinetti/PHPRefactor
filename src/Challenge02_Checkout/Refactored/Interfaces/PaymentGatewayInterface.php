<?php

namespace App\Challenge02_Checkout\Refactored\Interfaces;

interface PaymentGatewayInterface {
    public function charge(float $amount, string $token, string $email): void;
}