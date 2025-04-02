# MyLogin System

Um sistema de login moderno e seguro desenvolvido em PHP, com interface responsiva e recursos avançados de segurança.

## Funcionalidades

- Landing page informativa
- Registro de usuários
- Login seguro
- Gerenciamento de perfil
- Alteração de senha
- Interface responsiva
- Proteção contra SQL Injection
- Hash seguro de senhas
- Validação de dados
- Proteção XSS

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)

## Instalação

1. Clone ou baixe os arquivos para seu servidor web

2. Importe o banco de dados:

```sql
mysql -u root -p < database.sql
```

3. Configure o banco de dados em `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'mylogin');
```

4. Acesse o sistema pelo navegador:

```
http://seu-servidor/mylogin/landing.php
```

## Usuário de Teste

- Usuário: teste
- Senha: teste123

## Estrutura de Arquivos

- `landing.php` - Página inicial pública
- `login.php` - Página de login
- `register.php` - Página de registro
- `index.php` - Área do usuário
- `logout.php` - Script de logout
- `config.php` - Configurações do sistema
- `database.sql` - Estrutura do banco de dados

## Segurança

- Senhas armazenadas com hash seguro (password_hash)
- Proteção contra SQL Injection usando PDO
- Validação de dados de entrada
- Proteção contra XSS usando htmlspecialchars
- Controle de sessão
- Verificação de autenticação em páginas restritas

## Melhorias Futuras

- [ ] Recuperação de senha
- [ ] Confirmação de email
- [ ] Autenticação em duas etapas
- [ ] Upload de foto de perfil
- [ ] Histórico de login
- [ ] Tema escuro
- [ ] API REST

## Contribuição

Sinta-se à vontade para contribuir com o projeto através de pull requests ou reportando issues.

## Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
