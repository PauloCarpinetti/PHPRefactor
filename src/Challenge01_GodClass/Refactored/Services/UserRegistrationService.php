<?php

namespace App\Challenge01_GodClass\Refactored\Services;

use App\Challenge01_GodClass\Refactored\Validators\UserValidator;
use App\Challenge01_GodClass\Refactored\Repositories\UserRepository;
use RuntimeException;

class UserRegistrationService {

    private UserValidator $validator;
    private UserRepository $repository;
    private EmailService $emailService;

    // Injeção de Dependências facilita a criação de Mocks em testes unitários
    public function __construct(
        UserValidator $validator, 
        UserRepository $repository, 
        EmailService $emailService
    ) {
        $this->validator = $validator;
        $this->repository = $repository;
        $this->emailService = $emailService;
    }

    public function registerUser(array $postData): array {
        
        $this->validator->validate($postData);

        if ($this->repository->emailExists($postData['email'])) {
            throw new RuntimeException("Usuário já cadastrado.");
        }

        $hashedPassword = password_hash($postData['password'], PASSWORD_BCRYPT);
        
        $userId = $this->repository->save($postData['email'], $hashedPassword);

        $this->emailService->sendWelcomeEmail($postData['email']);

        return [
            'status' => 'success',
            'message' => 'Usuário criado com sucesso',
            'user_id' => $userId
        ];
    }
}