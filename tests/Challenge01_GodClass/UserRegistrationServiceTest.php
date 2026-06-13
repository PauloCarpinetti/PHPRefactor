<?php

namespace Tests\Challenge01_GodClass;

use App\Challenge01_GodClass\Refactored\Services\UserRegistrationService;
use App\Challenge01_GodClass\Refactored\Validators\UserValidator;
use App\Challenge01_GodClass\Refactored\Repositories\UserRepository;
use App\Challenge01_GodClass\Refactored\Services\EmailService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UserRegistrationServiceTest extends TestCase
{
    private $validatorMock;
    private $repositoryMock;
    private $emailServiceMock;
    private UserRegistrationService $service;

    // O método setUp roda antes de CADA teste. É ótimo para preparar o terreno.
    protected function setUp(): void
    {
        // 1. Criamos os "Mocks" (Objetos falsos que imitam as nossas classes reais)
        $this->validatorMock = $this->createMock(UserValidator::class);
        $this->repositoryMock = $this->createMock(UserRepository::class);
        $this->emailServiceMock = $this->createMock(EmailService::class);

        // 2. Injetamos os Mocks no nosso Serviço principal
        $this->service = new UserRegistrationService(
            $this->validatorMock,
            $this->repositoryMock,
            $this->emailServiceMock
        );
    }

    /**
     * Teste do "Caminho Feliz" (Happy Path)
     */
    public function testRegisterUserSuccessfully(): void
    {
        $postData = [
            'email' => 'teste@candidato.com',
            'password' => 'senhaSuperForte123'
        ];

        // Configuramos o Mock do Repositório para fingir que o email NÃO existe
        $this->repositoryMock->expects($this->once())
                             ->method('emailExists')
                             ->with('teste@candidato.com')
                             ->willReturn(false);

        // Configuramos o Mock do Repositório para fingir que salvou e retornou o ID 99
        $this->repositoryMock->expects($this->once())
                             ->method('save')
                             ->willReturn(99);

        // Configuramos o Mock do E-mail para garantir que ele tentou enviar o e-mail
        $this->emailServiceMock->expects($this->once())
                               ->method('sendWelcomeEmail')
                               ->with('teste@candidato.com');

        // Executamos o método real
        $result = $this->service->registerUser($postData);

        // Verificamos se o retorno é exatamente o que esperávamos
        $this->assertEquals('success', $result['status']);
        $this->assertEquals(99, $result['user_id']);
    }

    /**
     * Teste de Exceção (Regra de Negócio: E-mail Duplicado)
     */
    public function testThrowsExceptionIfUserAlreadyExists(): void
    {
        $postData = [
            'email' => 'duplicado@empresa.com',
            'password' => '12345678'
        ];

        // Configuramos o Mock para fingir que o usuário JÁ EXISTE no banco
        $this->repositoryMock->expects($this->once())
                             ->method('emailExists')
                             ->with('duplicado@empresa.com')
                             ->willReturn(true);

        // Garantimos que o método save NUNCA deve ser chamado se o e-mail já existe
        $this->repositoryMock->expects($this->never())
                             ->method('save');

        // Dizemos ao PHPUnit que ESPERAMOS que uma exceção seja lançada a partir daqui
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Usuário já cadastrado.");

        // Executamos o método (isso vai estourar a exceção e o PHPUnit vai dar "Pass")
        $this->service->registerUser($postData);
    }
}