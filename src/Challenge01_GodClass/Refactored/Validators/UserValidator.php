<?php

namespace App\Challenge01_GodClass\Refactored\Validators;

class UserValidator {
    public function validate(array $data): void {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email inválido.");
        }
        if (empty($data['password']) || strlen($data['password']) < 8) {
            throw new InvalidArgumentException("A senha deve ter pelo menos 8 caracteres.");
        }
    }
}
