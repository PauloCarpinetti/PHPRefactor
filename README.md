# 🛠️ PHP Legacy Code Refactoring & Architecture Challenges

Este repositório é uma coleção de exercícios práticos focados na modernização de sistemas legados em PHP. O objetivo é demonstrar a aplicação de boas práticas de engenharia de software, transformando códigos procedurais e "God Classes" em arquiteturas limpas, testáveis e orientadas a objetos.

## 🎯 Habilidades Demonstradas

* **Princípios SOLID** (Especialmente SRP e DIP)
* **Design Patterns** (Adapter, Dependency Injection)
* **Refatoração Estratégica** (Extract Class, Extract Method)
* **Ecossistema PHP** (Composer, PSR-4 Autoloading)
* **Desacoplamento de Infraestrutura** (Banco de Dados, APIs Externas, File System)

---

## 📂 Estrutura dos Desafios

Cada desafio está organizado em duas pastas principais dentro de `src/`:
* `/Legacy`: O cenário original problemático (alto acoplamento, difíceis de testar).
* `/Refactored`: A solução final aplicando arquitetura limpa e injeção de dependência.

### 1. Refatoração de God Class (Cadastro de Usuário)
Desmembramento de uma classe com múltiplas responsabilidades (Validação, Banco de Dados, Envio de E-mail) em serviços especialistas e repositórios.

### 2. Desacoplamento de Domínios (Checkout de E-commerce)
Separação da lógica de cálculo de carrinho, verificação de estoque e integrações com gateway de pagamento usando interfaces.

### 3. Padrão Adapter (Integração de APIs e Storage)
* **Cenário A:** Criação de um Adapter para fazer um sistema ERP legado consumir uma API REST moderna sem quebrar o contrato antigo.
* **Cenário B:** Substituição de salvamento de arquivos em disco local (Local Storage) para nuvem (Amazon S3) preservando o Princípio de Substituição de Liskov.

### 4. Modernização com Composer e PSR-4
Migração de scripts procedurais (`require_once`) baseados em funções globais para classes Orientadas a Objetos mapeadas pelo Autoloader do Composer.

---

## 🚀 Como Executar e Testar

Este projeto utiliza o Composer para gerenciar o autoload das classes refatoradas.

**1. Clone o repositório:**
```bash
git clone [https://github.com/PauloCarpinetti/PHPRefactor.git](https://github.com/PauloCarpinetti/PHPRefactor.git)
cd phprefactor
```
**2. Instale as dependências:**
```
composer install
```

**3. Testando o Autoload (Composer Scripts):**
```
composer dump-autoload
```

**4. Rodando os Testes Unitários:**
```
composer test
```