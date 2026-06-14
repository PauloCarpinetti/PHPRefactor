<?php

class CheckoutProcessor {
    
    public function processOrder(array $cartItems, array $customer, string $creditCardToken) {
        
        // 1. Cálculo do Total e Descontos
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            // Checagem de estoque misturada com cálculo
            $stock = file_get_contents("http://api.nossoestoque.com/check/" . $item['id']);
            if ((int)$stock < $item['qty']) {
                throw new Exception("Produto {$item['name']} sem estoque.");
            }
            $totalAmount += ($item['price'] * $item['qty']);
        }

        // Regra de desconto hardcoded
        if ($totalAmount > 1000) {
            $totalAmount = $totalAmount * 0.90; // 10% de desconto
        }

        // 2. Processamento do Pagamento (Simulando uma API externa)
        $ch = curl_init('https://api.gatewaypagamento.com/v1/charge');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $totalAmount,
            'token' => $creditCardToken,
            'email' => $customer['email']
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $paymentResponse = curl_exec($ch);
        $paymentStatus = json_decode($paymentResponse, true);

        if ($paymentStatus['status'] !== 'approved') {
            throw new Exception("Pagamento recusado.");
        }

        // 3. Salva no Banco de Dados
        $pdo = new PDO('mysql:host=localhost;dbname=loja', 'root', 'senha');
        $stmt = $pdo->prepare("INSERT INTO orders (customer_email, total, status) VALUES (?, ?, 'PAID')");
        $stmt->execute([$customer['email'], $totalAmount]);
        $orderId = $pdo->lastInsertId();

        // 4. Gera Recibo (Acoplamento com sistema de arquivos)
        $receipt = "Recibo do Pedido #{$orderId}\nTotal: R$ {$totalAmount}";
        file_put_contents("/var/www/receipts/order_{$orderId}.txt", $receipt);

        return [
            'success' => true,
            'order_id' => $orderId
        ];
    }
}