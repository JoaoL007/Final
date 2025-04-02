# ESCOLA SUPERIOR DE GESTÃO E CONTABILIDADE

![Logo ESGC](assets/images/logo.png)

<div style="text-align: center; margin-top: 100px;">
    <h1 style="font-size: 24px;">PROJETO FINAL DE REDES</h1>
    <h2 style="font-size: 36px; margin: 50px 0;">Sistema de Login Seguro</h2>
</div>

<div style="text-align: center; margin-top: 200px;">
    <p style="font-size: 18px;">Trabalho apresentado à disciplina de Redes</p>
    <p style="font-size: 18px;">Professor: Vera Rio Maior</p>
</div>

<div style="text-align: center; margin-top: 200px;">
    <p style="font-size: 18px;">Aluno: João Lima</p>
    <p style="font-size: 18px;">Data: 02 de Abril de 2025</p>
</div>

<div style="page-break-after: always;"></div>

# Sumário

1. [Introdução](#1-introdução)
   1. [Objetivo](#11-objetivo)
   2. [Tecnologias Utilizadas](#12-tecnologias-utilizadas)
2. [Estrutura do Projeto](#2-estrutura-do-projeto)
   1. [Organização de Arquivos](#21-organização-de-arquivos)
   2. [Banco de Dados](#22-banco-de-dados)
3. [Implementação](#3-implementação)
   1. [Diferenciação Client-Side vs Server-Side](#31-diferenciação-client-side-vs-server-side)
   2. [Segurança Implementada](#32-segurança-implementada)
   3. [Manipulação de Dados](#33-manipulação-de-dados)
4. [Interface do Usuário](#4-interface-do-usuário)
   1. [Design Responsivo](#41-design-responsivo)
   2. [Feedback Visual](#42-feedback-visual)
5. [Tratamento de Erros](#5-tratamento-de-erros)
   1. [Validação de Dados](#51-validação-de-dados)
   2. [Logs de Erro](#52-logs-de-erro)
6. [Screenshots](#6-screenshots)
   1. [Página de Login](#61-página-de-login)
   2. [Página de Registro](#62-página-de-registro)
   3. [Dashboard](#63-dashboard)
7. [Conclusão](#7-conclusão)
   1. [Objetivos Alcançados](#71-objetivos-alcançados)
   2. [Melhorias Futuras](#72-melhorias-futuras)
8. [Referências](#8-referências)

<div style="page-break-after: always;"></div>

# 1. Introdução

## 1.1 Objetivo

Este projeto foi desenvolvido como trabalho final da disciplina de Redes, ministrada pela professora Vera Rio Maior na Escola Superior de Gestão e Contabilidade (ESGC). O objetivo principal foi criar um sistema de login seguro e moderno utilizando PHP, MySQL e tecnologias web front-end, implementando funcionalidades essenciais de autenticação e gerenciamento de usuários, com foco em segurança e usabilidade.

### 1.2 Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor**: Apache/XAMPP

## 2. Estrutura do Projeto

### 2.1 Organização de Arquivos

```
/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── validation.js
├── includes/
│   ├── config.php
│   └── functions.php
├── docs/
│   ├── README.md
│   └── TECHNICAL.md
├── index.php
├── login.php
├── register.php
├── dashboard.php
└── database.sql
```

### 2.2 Banco de Dados

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 3. Implementação

### 3.1 Diferenciação Client-Side vs Server-Side

#### Client-Side (Navegador)

- Validação imediata de formulários
- Feedback visual em tempo real
- Melhoria na experiência do usuário

```javascript
// Exemplo de validação client-side
function validatePassword(password) {
  const hasUpperCase = /[A-Z]/.test(password);
  const hasNumbers = /\d/.test(password);
  return hasUpperCase && hasNumbers;
}
```

#### Server-Side (PHP)

- Validação final dos dados
- Processamento do banco de dados
- Gerenciamento de sessões

```php
// Exemplo de validação server-side
if (empty($username) || empty($password)) {
    $erro = "Preencha todos os campos";
} else {
    // Processamento seguro
}
```

### 3.2 Segurança Implementada

1. **Proteção contra SQL Injection**

   - Uso de PDO com prepared statements

   ```php
   $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
   $stmt->execute([$username]);
   ```

2. **Proteção contra XSS**

   - Escape de dados na saída

   ```php
   echo htmlspecialchars($user['nome']);
   ```

3. **Proteção CSRF**
   - Tokens únicos por formulário
   ```php
   <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
   ```

### 3.3 Manipulação de Dados

1. **Sessões**

   ```php
   session_start();
   $_SESSION['user_id'] = $user['id'];
   ```

2. **Cookies**
   ```php
   setcookie('remember_me', $token, [
       'expires' => time() + 30*24*60*60,
       'secure' => true,
       'httponly' => true
   ]);
   ```

## 4. Interface do Usuário

### 4.1 Design Responsivo

- Layout adaptável a diferentes dispositivos
- Uso de CSS Grid e Flexbox
- Media queries para responsividade

### 4.2 Feedback Visual

- Mensagens de erro claras
- Validação em tempo real
- Indicadores de carregamento

## 5. Tratamento de Erros

### 5.1 Validação de Dados

```php
function validarSenha($senha) {
    if (strlen($senha) < 8) {
        return "A senha deve ter pelo menos 8 caracteres";
    }
    return true;
}
```

### 5.2 Logs de Erro

```php
function logError($message) {
    $date = date('Y-m-d H:i:s');
    file_put_contents('error.log', "[$date] $message\n", FILE_APPEND);
}
```

## 6. Screenshots

### 6.1 Página de Login

![Login](screenshots/login.png)

- Formulário de login com validação
- Mensagens de erro claras
- Design moderno e limpo

### 6.2 Página de Registro

![Registro](screenshots/registro.png)

- Validação em tempo real
- Feedback visual imediato
- Campos obrigatórios marcados

### 6.3 Dashboard

![Dashboard](screenshots/dashboard.png)

- Interface intuitiva
- Informações do usuário
- Opções de gerenciamento

## 7. Conclusão

### 7.1 Objetivos Alcançados

- Sistema de login seguro implementado
- Interface responsiva e moderna
- Validações client e server-side
- Proteção contra vulnerabilidades comuns

### 7.2 Melhorias Futuras

- Implementação de recuperação de senha
- Autenticação em duas etapas
- Integração com redes sociais
- Perfil de usuário mais completo

## 8. Referências

1. PHP Documentation - https://www.php.net/docs.php
2. MySQL Documentation - https://dev.mysql.com/doc/
3. MDN Web Docs - https://developer.mozilla.org/
4. OWASP Security Guidelines - https://owasp.org/
5. Material didático da disciplina de Redes - ESGC

## Anexos

### A. Código Fonte

Os principais trechos de código estão disponíveis no repositório do projeto e foram desenvolvidos seguindo as melhores práticas apresentadas durante o curso.

### B. Scripts SQL

Todos os scripts necessários para criar e popular o banco de dados estão no arquivo `database.sql`.

### C. Declaração de Autoria

Eu, João Lima, declaro que este trabalho foi desenvolvido por mim e que todas as fontes utilizadas foram devidamente citadas no texto e listadas nas referências.

---

João Lima
02/04/2025
