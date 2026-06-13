<?php

class UserManager {
    
    public function registerUser($postData) {
        // 1. Validação
        if (empty($postData['email']) || !filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido.");
        }
        if (empty($postData['password']) || strlen($postData['password']) < 8) {
            throw new Exception("A senha deve ter pelo menos 8 caracteres.");
        }

        // 2. Conexão com o Banco de Dados
        $dsn = 'mysql:host=localhost;dbname=meubanco';
        $username = 'root';
        $password = 'senha123';
        $pdo = new PDO($dsn, $username, $password);

        // 3. Verifica se o usuário já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $postData['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Usuário já cadastrado.");
        }

        // 4. Salva o novo usuário
        $hashedPassword = password_hash($postData['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, created_at) VALUES (:email, :password, NOW())");
        $stmt->execute([
            'email' => $postData['email'],
            'password' => $hashedPassword
        ]);
        $userId = $pdo->lastInsertId();

        // 5. Envia email de boas-vindas
        $to = $postData['email'];
        $subject = "Bem-vindo ao nosso sistema!";
        $message = "Olá, sua conta foi criada com sucesso.";
        $headers = "From: no-reply@empresa.com";
        mail($to, $subject, $message, $headers);

        // 6. Retorna resposta
        return [
            'status' => 'success',
            'message' => 'Usuário criado com sucesso',
            'user_id' => $userId
        ];
    }
}