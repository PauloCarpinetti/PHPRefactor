<?php

namespace App\Challenge01_GodClass\Refactored\Services;

class EmailService {
    
    public function sendWelcomeEmail(string $to): void {
    $subject = "Bem-vindo ao nosso sistema!";
    $message = "Olá, sua conta foi criada com sucesso.";
    $headers = "From: no-reply@empresa.com";
    // Em um sistema real, aqui iria a lógica de uma biblioteca como o PHPMailer
    mail($to, $subject, $message, $headers);
    }
    
}