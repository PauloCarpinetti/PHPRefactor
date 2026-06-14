<?php

namespace App\Challenge02_Checkout\Refactored\GateWays;

use App\Challenge02_Checkout\Refactored\Interfaces\PaymentGatewayInterface;
use RuntimeException;

class StripePaymentGateway implements PaymentGatewayInterface {
    public function charge(float $amount, string $token, string $email): void {
        
        $ch = curl_init('https://api.gatewaypagamento.com/v1/charge');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $amount,
            'token' => $token,
            'email' => $email
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $paymentResponse = curl_exec($ch);
        $paymentStatus = json_decode($paymentResponse, true);

        if ($paymentStatus['status'] !== 'approved') {
            throw new RuntimeException("Pagamento recusado.");
        }
        
    }
}