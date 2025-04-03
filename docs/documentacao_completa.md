# Sistema MyLogin - Documentação Completa

<div style="text-align: center;">
    <h1 style="font-size: 36px;">Sistema de Login Seguro</h1>
    <p>Versão 1.0</p>
</div>

## Sumário

1. [Introdução](#1-introdução)
   1. [Objetivo](#11-objetivo)
   2. [Tecnologias Utilizadas](#12-tecnologias-utilizadas)
2. [Estrutura do Projeto](#2-estrutura-do-projeto)
   1. [Organização de Arquivos](#21-organização-de-arquivos)
   2. [Banco de Dados](#22-banco-de-dados)
3. [Funcionalidades](#3-funcionalidades)
   1. [Sistema de Autenticação](#31-sistema-de-autenticação)
   2. [Gerenciamento de Perfil](#32-gerenciamento-de-perfil)
   3. [Interface](#33-interface)
4. [Implementação Técnica](#4-implementação-técnica)
   1. [Diferenciação Client-Side vs Server-Side](#41-diferenciação-client-side-vs-server-side)
   2. [Estruturas de Controle](#42-estruturas-de-controle)
   3. [Manipulação de Dados](#43-manipulação-de-dados)
   4. [Fluxos Principais](#44-fluxos-principais)
5. [Segurança](#5-segurança)
   1. [Proteção contra SQL Injection](#51-proteção-contra-sql-injection)
   2. [Proteção XSS](#52-proteção-xss)
   3. [Proteção CSRF](#53-proteção-csrf)
   4. [Segurança de Senhas](#54-segurança-de-senhas)
   5. [Controle de Sessão e Cookies](#55-controle-de-sessão-e-cookies)
6. [Tratamento de Erros](#6-tratamento-de-erros)
   1. [Validação de Dados](#61-validação-de-dados)
   2. [Logs de Erro](#62-logs-de-erro)
7. [Interface do Usuário](#7-interface-do-usuário)
   1. [Design Responsivo](#71-design-responsivo)
   2. [Feedback Visual](#72-feedback-visual)
8. [Implementação da Foto de Perfil](#8-implementação-da-foto-de-perfil)
9. [Instalação e Configuração](#9-instalação-e-configuração)
   1. [Requisitos](#91-requisitos)
   2. [Processo de Instalação](#92-processo-de-instalação)
   3. [Configuração](#93-configuração)
10. [Screenshots](#10-screenshots)
11. [Conclusão](#11-conclusão)
    1. [Objetivos Alcançados](#111-objetivos-alcançados)
    2. [Melhorias Futuras](#112-melhorias-futuras)
12. [Referências](#12-referências)

## 1. Introdução

### 1.1 Objetivo

Este projeto implementa um sistema de login seguro e moderno, desenvolvido em PHP com banco de dados MySQL. O sistema foi criado para demonstrar boas práticas de desenvolvimento web, com foco em segurança, usabilidade e design responsivo. Oferece funcionalidades de autenticação completas, incluindo registro de usuários, login seguro, gerenciamento de perfil e proteção contra vulnerabilidades comuns.

### 1.2 Tecnologias Utilizadas

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

## 2. Estrutura do Projeto

### 2.1 Organização de Arquivos

```
/
├── assets/             # Recursos estáticos (imagens, scripts)
├── docs/               # Documentação
├── uploads/            # Diretório para armazenar fotos de perfil
├── screenshots/        # Capturas de tela do sistema
├── config.php          # Configurações e funções globais
├── database.sql        # Estrutura do banco de dados
├── landing.php         # Página inicial pública
├── login.php           # Página de login
├── logout.php          # Script de logout
├── register.php        # Página de registro
├── dashboard.php       # Painel do usuário
├── index.php           # Redirecionamento para área do usuário
├── style.css           # Estilos globais
└── README.md           # Instruções básicas
```

### 2.2 Banco de Dados

O sistema utiliza um banco de dados MySQL com as seguintes tabelas:

#### Tabela `users`

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    foto_perfil VARCHAR(255) DEFAULT NULL
);
```

#### Tabela `remember_tokens`

```sql
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 3. Funcionalidades

### 3.1 Sistema de Autenticação

- **Login com username/senha**

  - Validação de credenciais
  - Tratamento de tentativas inválidas
  - Feedback claro de erros

- **Registro de novos usuários**

  - Validação de dados
  - Verificação de usernames e emails duplicados
  - Confirmação de cadastro

- **Função "Lembrar-me"**

  - Cookies seguros
  - Tokens de autenticação persistente
  - Rotação de tokens para maior segurança

- **Segurança avançada**
  - Limite de tentativas de login
  - Bloqueio temporário após tentativas falhas
  - Proteção contra ataques de força bruta

### 3.2 Gerenciamento de Perfil

- **Visualização de dados do usuário**

  - Dashboard personalizado
  - Exibição de informações cadastrais

- **Atualização de informações**

  - Edição de dados pessoais
  - Validação em tempo real
  - Feedback de sucesso/erro

- **Alteração de senha**

  - Verificação de senha atual
  - Requisitos de força de senha
  - Atualização segura

- **Upload de foto de perfil**
  - Suporte a formatos comuns de imagem
  - Armazenamento seguro
  - Remoção de fotos existentes

### 3.3 Interface

- **Design responsivo**

  - Adaptação a diferentes dispositivos
  - Layout fluido
  - Uso de media queries

- **Temas e estilos**

  - Variáveis CSS para fácil personalização
  - Elementos visuais consistentes
  - Paleta de cores harmônica

- **Feedback visual**
  - Mensagens de sucesso/erro
  - Indicadores de carregamento
  - Validação em tempo real

## 4. Implementação Técnica

### 4.1 Diferenciação Client-Side vs Server-Side

#### Client-Side (Frontend)

O código executado no navegador do usuário:

```javascript
// Exemplo de validação client-side
function validatePassword(password) {
  const hasUpperCase = /[A-Z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasMinLength = password.length >= 8;

  if (!hasMinLength) {
    showError("A senha deve ter pelo menos 8 caracteres");
    return false;
  }

  if (!hasUpperCase) {
    showError("A senha deve ter pelo menos uma letra maiúscula");
    return false;
  }

  if (!hasNumbers) {
    showError("A senha deve ter pelo menos um número");
    return false;
  }

  return true;
}
```

#### Server-Side (Backend)

O código executado no servidor:

```php
// Exemplo de validação server-side
function validarSenha($senha) {
    if (strlen($senha) < PASSWORD_MIN_LENGTH) {
        return "A senha deve ter pelo menos " . PASSWORD_MIN_LENGTH . " caracteres";
    }
    if (!preg_match("/[A-Z]/", $senha)) {
        return "A senha deve conter pelo menos uma letra maiúscula";
    }
    if (!preg_match("/[0-9]/", $senha)) {
        return "A senha deve conter pelo menos um número";
    }
    return true;
}
```

### 4.2 Estruturas de Controle

#### Condicionais

```php
// Exemplo de if/else para validação
if (empty($username)) {
    $erro = "Nome de usuário é obrigatório";
} else if (strlen($username) < 3) {
    $erro = "Nome de usuário deve ter pelo menos 3 caracteres";
} else {
    // Processamento do username válido
}
```

#### Loops

```php
// Exemplo de foreach para processar dados
foreach ($validations as $field => $rules) {
    foreach ($rules as $rule) {
        if (!$rule($data[$field])) {
            $errors[$field] = "Erro de validação no campo {$field}";
        }
    }
}
```

### 4.3 Manipulação de Dados

#### Arrays

```php
// Exemplo de manipulação de array
$userdata = [
    'username' => $username,
    'email' => $email,
    'nome' => $nome,
    'created_at' => date('Y-m-d H:i:s')
];
```

#### Banco de Dados

```php
// Exemplo de CRUD - Create
function createUser($data) {
    global $pdo;
    $sql = "INSERT INTO users (username, email, nome, password) VALUES (?, ?, ?, ?)";
    return $pdo->prepare($sql)->execute([
        $data['username'],
        $data['email'],
        $data['nome'],
        password_hash($data['password'], PASSWORD_DEFAULT)
    ]);
}

// Exemplo de CRUD - Read
function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
```

### 4.4 Fluxos Principais

#### Registro de Usuário

1. Usuário acessa a página de registro
2. Preenche dados pessoais e credenciais
3. Cliente valida os dados em tempo real (JavaScript)
4. Envio do formulário
5. Servidor valida os dados (PHP)
6. Sistema verifica disponibilidade de username e email
7. Cria conta com senha encriptada
8. Redireciona para login com mensagem de sucesso

#### Autenticação

1. Usuário fornece credenciais
2. Sistema verifica número de tentativas de login
3. Valida credenciais contra banco de dados
4. Se solicitado, cria cookie "lembrar-me" com token seguro
5. Inicia sessão autenticada
6. Regenera ID de sessão para segurança
7. Redireciona para área restrita (dashboard)

#### Gerenciamento de Perfil

1. Usuário acessa painel após login
2. Pode visualizar e atualizar dados pessoais
3. Pode fazer upload de foto de perfil
4. Pode alterar senha (exige senha atual para verificação)
5. Recebe feedback visual sobre as operações realizadas

## 5. Segurança

### 5.1 Proteção contra SQL Injection

Todas as consultas ao banco de dados são realizadas usando PDO com prepared statements, evitando a concatenação direta de strings SQL:

```php
// Consulta segura com prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
```

### 5.2 Proteção XSS

Todo o conteúdo gerado pelo usuário é sanitizado antes de ser exibido:

```php
// Sanitização na saída
echo htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8');
```

E para dados recebidos:

```php
// Sanitização na entrada
function limparDados($dados) {
    if (is_array($dados)) {
        return array_map('limparDados', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}
```

### 5.3 Proteção CSRF

Uso de tokens para proteger contra ataques de falsificação de requisição:

```php
// Geração de token
function gerarCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validação de token
function validarCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

No formulário:

```html
<input
  type="hidden"
  name="csrf_token"
  value="<?php echo gerarCSRFToken(); ?>"
/>
```

### 5.4 Segurança de Senhas

Senhas são armazenadas usando o algoritmo bcrypt através da função `password_hash()`:

```php
// Hash seguro
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Verificação
if (password_verify($senha_fornecida, $hash_armazenado)) {
    // Senha correta
}
```

### 5.5 Controle de Sessão e Cookies

Configurações seguras para sessão:

```php
// Configurações de sessão
ini_set('session.cookie_httponly', 1);    // Previne acesso via JavaScript
ini_set('session.use_only_cookies', 1);   // Força uso apenas de cookies
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Cookie seguro em HTTPS

// Regeneração de ID após login
session_regenerate_id(true);
```

Cookies seguros para "lembrar-me":

```php
// Cookie seguro
setcookie('remember_me', $token, [
    'expires' => time() + REMEMBER_ME_DURATION,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);
```

## 6. Tratamento de Erros

### 6.1 Validação de Dados

Validação completa em todos os formulários:

```php
// Exemplo de função de validação
function validarCampos($dados) {
    $erros = [];

    // Valida nome
    if (empty($dados['nome'])) {
        $erros['nome'] = "Nome é obrigatório";
    }

    // Valida email
    if (empty($dados['email'])) {
        $erros['email'] = "Email é obrigatório";
    } else if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = "Email inválido";
    }

    // Valida senha
    if (isset($dados['senha'])) {
        $resultadoSenha = validarSenha($dados['senha']);
        if ($resultadoSenha !== true) {
            $erros['senha'] = $resultadoSenha;
        }
    }

    return $erros;
}
```

### 6.2 Logs de Erro

Sistema de registro de erros para depuração:

```php
// Função para registrar erros
function logError($mensagem) {
    $data = date('Y-m-d H:i:s');
    $log = "[{$data}] {$mensagem}\n";
    file_put_contents('error.log', $log, FILE_APPEND);
}
```

Tratamento de exceções:

```php
try {
    // Operação que pode falhar
    $result = operacaoArriscada();
} catch (Exception $e) {
    // Registra o erro
    logError($e->getMessage());
    // Retorna mensagem amigável para o usuário
    $mensagem = "Ocorreu um erro inesperado. Por favor, tente novamente.";
}
```

## 7. Interface do Usuário

### 7.1 Design Responsivo

Design que se adapta a diferentes tamanhos de tela:

```css
/* Design base */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Responsividade */
@media (max-width: 768px) {
  .nav-container {
    flex-direction: column;
    gap: 1rem;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }
}
```

### 7.2 Feedback Visual

Feedback claro para ações do usuário:

```php
// Exemplo de exibição de mensagem
if ($mensagem) {
    echo '<div class="message">' . htmlspecialchars($mensagem) . '</div>';
}
```

Estilização das mensagens:

```css
/* Estilo para mensagens de feedback */
.message {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 4px;
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
```

## 8. Implementação da Foto de Perfil

O sistema permite que usuários façam upload de fotos de perfil:

1. **Estrutura do formulário**:

```html
<form method="POST" enctype="multipart/form-data">
  <!-- Outros campos -->
  <div class="form-group">
    <label for="foto_perfil">Foto de Perfil:</label>
    <input
      type="file"
      id="foto_perfil"
      name="foto_perfil"
      class="form-control"
    />
  </div>
  <button type="submit" class="btn">Atualizar</button>
</form>
```

2. **Processamento do upload**:

```php
if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $foto = $_FILES['foto_perfil'];
    $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
    $permitidos = ['jpg', 'jpeg', 'png'];

    if(in_array(strtolower($ext), $permitidos)) {
        $nome_arquivo = 'uploads/perfil_' . $_SESSION['user_id'] . '.' . $ext;

        if(move_uploaded_file($foto['tmp_name'], $nome_arquivo)) {
            // Atualiza banco de dados
            $stmt = $pdo->prepare("UPDATE users SET foto_perfil = ? WHERE id = ?");
            $stmt->execute([$nome_arquivo, $_SESSION['user_id']]);
        }
    }
}
```

3. **Exibição da foto**:

```php
<div class="user-avatar">
    <?php if(isset($user['foto_perfil']) && !empty($user['foto_perfil']) && file_exists($user['foto_perfil'])): ?>
        <img src="<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de perfil">
    <?php else: ?>
        <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
    <?php endif; ?>
</div>
```

4. **Estilização**:

```css
.user-avatar {
  width: 100px;
  height: 100px;
  background-color: var(--primary-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  overflow: hidden;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
```

## 9. Instalação e Configuração

### 9.1 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP: PDO, GD (para manipulação de imagens)
- Servidor web (Apache/Nginx)
- Permissões de escrita para o diretório `uploads/`

### 9.2 Processo de Instalação

1. Clone ou baixe os arquivos para seu servidor web:

```bash
git clone https://github.com/seu-usuario/Final.git
```

2. Importe o banco de dados:

```bash
mysql -u root -p < database.sql
```

3. Certifique-se de que o diretório `uploads/` tem permissões de escrita:

```bash
chmod 755 uploads/
```

### 9.3 Configuração

Edite o arquivo `config.php` conforme suas configurações:

```php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'mylogin');

// Outras configurações
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_LIFETIME', 3600);
```

## 10. Screenshots

### Página de Login

![Login](screenshots/login.png)

### Página de Registro

![Registro](screenshots/registro.png)

### Dashboard

![Dashboard](screenshots/dashboard.png)

## 11. Conclusão

### 11.1 Objetivos Alcançados

- Sistema de login seguro implementado
- Interface responsiva e moderna
- Validações client e server-side
- Proteção contra vulnerabilidades comuns
- Upload e gerenciamento de foto de perfil
- Documentação completa

### 11.2 Melhorias Futuras

- Implementação de recuperação de senha
- Autenticação em duas etapas
- Integração com redes sociais
- Histórico de login
- Tema escuro
- API REST

## 12. Referências

1. PHP Documentation - https://www.php.net/docs.php
2. MySQL Documentation - https://dev.mysql.com/doc/
3. MDN Web Docs - https://developer.mozilla.org/
4. OWASP Security Guidelines - https://owasp.org/
5. Material didático de cursos e livros sobre desenvolvimento web
