# Sistema de Login Seguro - Documentação

## Sumário

1. [Introdução](#introdução)
2. [Tecnologias Utilizadas](#tecnologias-utilizadas)
3. [Estrutura do Projeto](#estrutura-do-projeto)
4. [Funcionalidades](#funcionalidades)
5. [Segurança](#segurança)
6. [Client-Side vs Server-Side](#client-side-vs-server-side)
7. [Instalação e Configuração](#instalação-e-configuração)
8. [Exemplos de Uso](#exemplos-de-uso)

## Introdução

Este projeto implementa um sistema de login seguro e moderno, desenvolvido em PHP com banco de dados MySQL. O sistema inclui funcionalidades avançadas de segurança, interface responsiva e gerenciamento de usuários.

## Tecnologias Utilizadas

- **Frontend**:
  - HTML5
  - CSS3 (com variáveis CSS e design responsivo)
  - JavaScript (validação client-side)
- **Backend**:
  - PHP 7.4+
  - MySQL 5.7+
  - PDO para conexão com banco de dados
- **Segurança**:
  - Password hashing (bcrypt)
  - Proteção contra SQL Injection
  - Proteção CSRF
  - Sanitização de dados
  - Controle de sessão seguro

## Estrutura do Projeto

```
/
├── assets/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── functions.php
│   └── db.php
├── docs/
│   └── README.md
├── landing.php
├── login.php
├── register.php
├── index.php
├── logout.php
├── database.sql
└── error.log
```

## Funcionalidades

### 1. Sistema de Autenticação

- Login com username/senha
- Registro de novos usuários
- Função "Lembrar-me"
- Limite de tentativas de login
- Bloqueio temporário após tentativas falhas

### 2. Gerenciamento de Perfil

- Visualização de dados do usuário
- Atualização de informações
- Alteração de senha
- Preferências do usuário (tema, idioma)

### 3. Segurança

- Validação de força de senha
- Tokens CSRF em formulários
- Sanitização de dados
- Logs de acesso
- Sessões seguras

### 4. Interface

- Design responsivo
- Temas claro/escuro
- Feedback visual de ações
- Mensagens de erro amigáveis

## Client-Side vs Server-Side

### Client-Side (Frontend)

```javascript
// Exemplo de validação client-side
function validateForm() {
  const password = document.getElementById("password").value;
  if (password.length < 8) {
    showError("A senha deve ter pelo menos 8 caracteres");
    return false;
  }
  return true;
}
```

### Server-Side (Backend)

```php
// Exemplo de validação server-side
function validarSenha($senha) {
    if (strlen($senha) < PASSWORD_MIN_LENGTH) {
        return "A senha deve ter pelo menos " . PASSWORD_MIN_LENGTH . " caracteres";
    }
    return true;
}
```

## Segurança

### 1. Proteção Contra SQL Injection

```php
// Uso de prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### 2. Proteção CSRF

```php
// Geração de token
$token = gerarCSRFToken();

// Validação
if (!validarCSRFToken($_POST['csrf_token'])) {
    die("Token CSRF inválido");
}
```

### 3. Sanitização de Dados

```php
// Limpeza de dados de entrada
$dados = limparDados($_POST);
```

## Instalação e Configuração

1. **Requisitos**:

   - PHP 7.4 ou superior
   - MySQL 5.7 ou superior
   - Servidor web (Apache/Nginx)

2. **Instalação**:

   ```bash
   # Clone o repositório
   git clone https://github.com/seu-usuario/mylogin.git

   # Importe o banco de dados
   mysql -u root -p < database.sql
   ```

3. **Configuração**:
   - Edite `includes/config.php` com suas configurações
   - Ajuste as permissões de arquivos
   - Configure o servidor web

## Exemplos de Uso

### 1. Login

```php
// Exemplo de login
$user = loginUser($username, $password);
if ($user) {
    $_SESSION['user_id'] = $user['id'];
    redirect('index.php');
}
```

### 2. Registro

```php
// Exemplo de registro
$dados = [
    'username' => 'usuario',
    'password' => 'Senha@123',
    'email' => 'usuario@email.com',
    'nome' => 'Nome Completo'
];
registerUser($dados);
```

### 3. Atualização de Perfil

```php
// Exemplo de atualização
$dados = [
    'nome' => 'Novo Nome',
    'email' => 'novo@email.com'
];
updateUser($userId, $dados);
```

## Screenshots

### Página de Login

![Login](screenshots/login.png)

### Página de Registro

![Registro](screenshots/registro.png)

### Dashboard

![Dashboard](screenshots/dashboard.png)

## Contribuição

Contribuições são bem-vindas! Por favor, leia o arquivo CONTRIBUTING.md para detalhes sobre nosso código de conduta e processo de envio de pull requests.
