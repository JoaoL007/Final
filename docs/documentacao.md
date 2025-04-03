# Documentação do Sistema MyLogin

## Visão Geral

O MyLogin é um sistema de autenticação e gerenciamento de usuários desenvolvido em PHP puro com MySQL. O sistema oferece funcionalidades de registro, login, gerenciamento de perfil e segurança avançada.

## Estrutura do Projeto

```
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

## Componentes Principais

### 1. Banco de Dados (database.sql)

O sistema utiliza um banco de dados MySQL com as seguintes tabelas:

- **users**: Armazena informações dos usuários (id, nome, email, username, password, data_registro, foto_perfil)
- **remember_tokens**: Armazena tokens para a funcionalidade "Lembrar-me" (id, user_id, token, expires_at, used)

### 2. Configuração (config.php)

Arquivo central de configuração que contém:

- Constantes de configuração do banco de dados
- Configurações de segurança
- Configurações de sessão
- Funções auxiliares para:
  - Conexão com o banco de dados
  - Autenticação e verificação de login
  - Validação de dados
  - Proteção contra CSRF
  - Registro de erros
  - Gerenciamento de tentativas de login

### 3. Páginas Principais

#### Landing Page (landing.php)

Página inicial pública que apresenta o sistema aos visitantes e oferece links para login e registro.

#### Login (login.php)

Página de autenticação com:

- Validação de credenciais
- Proteção contra excesso de tentativas
- Opção "Lembrar-me"
- Redirecionamento para área restrita

#### Registro (register.php)

Página de cadastro com:

- Validação de dados
- Verificação de disponibilidade de username
- Encriptação segura de senhas
- Criação de conta de usuário

#### Painel do Usuário (dashboard.php)

Área restrita que permite:

- Visualização de informações do perfil
- Atualização de dados pessoais
- Upload e gerenciamento de foto de perfil
- Alteração de senha

#### Logout (logout.php)

Script responsável por encerrar a sessão do usuário e invalidar cookies.

### 4. Estilos (style.css)

Arquivo CSS que contém todos os estilos do sistema, organizados em seções:

- Variáveis de cores e temas
- Elementos base (corpo, cabeçalhos)
- Componentes de navegação
- Cards e containers
- Formulários e controles
- Mensagens e alertas
- Avatar e imagens de perfil
- Responsividade

## Fluxos Principais

### Registro de Usuário

1. Usuário acessa a página de registro
2. Preenche dados pessoais e credenciais
3. Sistema valida os dados
4. Verifica disponibilidade de username
5. Cria conta com senha encriptada
6. Redireciona para login

### Autenticação

1. Usuário fornece credenciais
2. Sistema verifica número de tentativas
3. Valida credenciais contra banco de dados
4. Se solicitado, cria cookie "lembrar-me"
5. Inicia sessão autenticada
6. Redireciona para área restrita

### Gerenciamento de Perfil

1. Usuário acessa painel após login
2. Pode atualizar dados pessoais
3. Pode fazer upload de foto de perfil
4. Pode alterar senha (exige senha atual)

## Segurança Implementada

O sistema implementa diversas camadas de segurança:

- **Proteção de Senha**: Utiliza `password_hash()` e `password_verify()` para armazenamento seguro
- **Proteção contra SQL Injection**: Uso de PDO com prepared statements
- **Proteção XSS**: Sanitização de saída com `htmlspecialchars()`
- **Proteção CSRF**: Tokens de validação em formulários
- **Bloqueio de Tentativas**: Limitação de tentativas de login consecutivas
- **Cookies Seguros**: Flags httpOnly e secure quando em HTTPS
- **Validação de Dados**: Verificação de todos os dados recebidos
- **Controle de Sessão**: Configurações seguras e renovação de sessão após login

## Funções Auxiliares

O sistema conta com diversas funções auxiliares em `config.php`:

- `conectarDB()`: Estabelece conexão segura com o banco de dados
- `estaLogado()`: Verifica se o usuário está autenticado
- `requireLogin()`: Redireciona usuários não autenticados
- `validarSenha()`: Verifica requisitos de segurança da senha
- `gerarCSRFToken()`: Gera token para proteção contra CSRF
- `validarCSRFToken()`: Valida token CSRF recebido
- `logError()`: Registra erros em arquivo de log
- `limparDados()`: Sanitiza dados de entrada
- `verificarTentativasLogin()`: Controla tentativas de login
- `criarCookieLembrarMe()`: Gerencia cookie para autenticação persistente

## Implementação da Foto de Perfil

O sistema permite que usuários façam upload de fotos de perfil:

1. O formulário usa `enctype="multipart/form-data"` para suportar upload
2. A foto é validada quanto a tipo (jpg, jpeg, png) e tamanho
3. O arquivo é salvo no diretório `uploads/` com nome único baseado no ID do usuário
4. O caminho é armazenado na coluna `foto_perfil` da tabela `users`
5. É possível remover a foto existente através de checkbox
6. A foto é exibida no avatar do usuário quando disponível

## Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP: PDO, GD (para manipulação de imagens)
- Permissões de escrita para o diretório `uploads/`

## Considerações de Manutenção

- **Logs**: Erros são registrados em `error.log`
- **Segurança**: Atualizar configurações em `config.php` conforme necessário
- **Backup**: Realizar backup regular do banco de dados
- **Uploads**: Monitorar o diretório de uploads para evitar excesso de armazenamento
