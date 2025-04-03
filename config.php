<?php

/**
 * Arquivo de configuração principal do MyLogin System
 * 
 * Este arquivo contém todas as configurações do sistema, constantes,
 * configurações de segurança e funções auxiliares utilizadas em todo o projeto.
 * É importado por todas as páginas que necessitam de configurações ou funções comuns.
 * 
 * @author Seu Nome
 * @version 1.0
 */

// ====================================
// CONFIGURAÇÕES DO BANCO DE DADOS
// ====================================
/**
 * Constantes de conexão com o banco de dados MySQL
 */
define('DB_HOST', 'localhost');       // Servidor de banco de dados
define('DB_USER', 'root');            // Usuário do banco de dados
define('DB_PASS', '');                // Senha do banco de dados
define('DB_NAME', 'mylogin');         // Nome do banco de dados

// ====================================
// CONFIGURAÇÕES DE SEGURANÇA
// ====================================
/**
 * Constantes que definem parâmetros de segurança do sistema
 */
define('MAX_LOGIN_ATTEMPTS', 3);          // Máximo de tentativas de login antes do bloqueio
define('LOGIN_TIMEOUT', 900);             // Tempo de bloqueio após tentativas (15 minutos)
define('PASSWORD_MIN_LENGTH', 8);         // Tamanho mínimo da senha
define('SESSION_LIFETIME', 3600);         // Duração da sessão (1 hora)
define('REMEMBER_ME_DURATION', 2592000);  // Duração do cookie "lembrar-me" (30 dias)

// ====================================
// CONFIGURAÇÕES DE SESSÃO
// ====================================
/**
 * Configurações para aumentar a segurança da sessão
 */
ini_set('session.cookie_httponly', 1);    // Previne acesso via JavaScript
ini_set('session.use_only_cookies', 1);   // Força uso apenas de cookies
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Cookie seguro em HTTPS

// Inicia a sessão para todas as páginas que importam este arquivo
session_start();

/**
 * Função para conectar ao banco de dados usando PDO
 * 
 * Estabelece uma conexão segura com o banco de dados MySQL
 * usando PDO com configurações de segurança apropriadas
 * 
 * @return PDO Objeto PDO para conexão com o banco de dados
 * @throws PDOException Caso ocorra erro na conexão
 */
function conectarDB()
{
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Lança exceções em caso de erro
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    // Retorna resultados como arrays associativos
                PDO::ATTR_EMULATE_PREPARES => false                 // Desabilita emulação de prepared statements
            )
        );
        return $pdo;
    } catch (PDOException $e) {
        logError('Database connection failed: ' . $e->getMessage());
        die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
    }
}

/**
 * Função para verificar se o usuário está logado
 * 
 * Verifica se existe uma sessão ativa de usuário
 * 
 * @return bool True se o usuário está logado, False caso contrário
 */
function estaLogado()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Função para redirecionar usuários não autenticados
 * 
 * Redireciona para a página de login se o usuário não estiver autenticado
 * Útil para proteger páginas que requerem autenticação
 * 
 * @return void
 */
function requireLogin()
{
    if (!estaLogado()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Função para validar a força da senha
 * 
 * Verifica se a senha atende aos requisitos mínimos de segurança:
 * - Comprimento mínimo
 * - Presença de letra maiúscula
 * - Presença de letra minúscula
 * - Presença de número
 * 
 * @param string $senha A senha a ser validada
 * @return mixed True se a senha é válida, ou mensagem de erro
 */
function validarSenha($senha)
{
    if (strlen($senha) < PASSWORD_MIN_LENGTH) {
        return "A senha deve ter pelo menos " . PASSWORD_MIN_LENGTH . " caracteres";
    }
    if (!preg_match("/[A-Z]/", $senha)) {
        return "A senha deve conter pelo menos uma letra maiúscula";
    }
    if (!preg_match("/[a-z]/", $senha)) {
        return "A senha deve conter pelo menos uma letra minúscula";
    }
    if (!preg_match("/[0-9]/", $senha)) {
        return "A senha deve conter pelo menos um número";
    }
    return true;
}

/**
 * Função para gerar token CSRF
 * 
 * Gera um token aleatório para proteção contra ataques CSRF
 * (Cross-Site Request Forgery)
 * 
 * @return string Token CSRF gerado
 */
function gerarCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Função para validar token CSRF
 * 
 * Compara o token recebido com o armazenado na sessão
 * para prevenir ataques CSRF
 * 
 * @param string $token O token CSRF a ser validado
 * @return bool True se o token é válido, False caso contrário
 */
function validarCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Função para registrar erros em log
 * 
 * Salva mensagens de erro em um arquivo de log com data e hora
 * 
 * @param string $mensagem A mensagem de erro a ser registrada
 * @return void
 */
function logError($mensagem)
{
    $data = date('Y-m-d H:i:s');
    $log = "[{$data}] {$mensagem}\n";
    file_put_contents('error.log', $log, FILE_APPEND);
}

/**
 * Função para limpar dados de entrada
 * 
 * Sanitiza dados de entrada para prevenir XSS e outros ataques
 * 
 * @param mixed $dados Os dados a serem sanitizados
 * @return mixed Os dados sanitizados
 */
function limparDados($dados)
{
    if (is_array($dados)) {
        return array_map('limparDados', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para verificar tentativas de login
 * 
 * Controla o número de tentativas de login para um username
 * e implementa bloqueio temporário após muitas tentativas
 * 
 * @param string $username O nome de usuário a verificar
 * @return mixed True se pode tentar login, ou mensagem de erro
 */
function verificarTentativasLogin($username)
{
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = [
            'count' => 0,
            'time' => time()
        ];
    }

    $attempts = &$_SESSION['login_attempts'][$username];

    // Reseta as tentativas após o timeout
    if ((time() - $attempts['time']) > LOGIN_TIMEOUT) {
        $attempts['count'] = 0;
        $attempts['time'] = time();
        return true;
    }

    // Verifica se excedeu o limite de tentativas
    if ($attempts['count'] >= MAX_LOGIN_ATTEMPTS) {
        $timeLeft = LOGIN_TIMEOUT - (time() - $attempts['time']);
        return "Muitas tentativas de login. Tente novamente em " . ceil($timeLeft / 60) . " minutos.";
    }

    return true;
}

/**
 * Função para incrementar tentativas de login
 * 
 * Aumenta o contador de tentativas de login para um username
 * 
 * @param string $username O nome de usuário
 * @return void
 */
function incrementarTentativasLogin($username)
{
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = [
            'count' => 0,
            'time' => time()
        ];
    }
    $_SESSION['login_attempts'][$username]['count']++;
}

/**
 * Função para criar cookie "lembrar-me"
 * 
 * Gera um token seguro e o armazena no banco de dados e em cookie
 * para permitir autenticação persistente
 * 
 * @param int $userId ID do usuário
 * @return void
 */
function criarCookieLembrarMe($userId)
{
    $token = bin2hex(random_bytes(32));
    $expiry = time() + REMEMBER_ME_DURATION;

    $pdo = conectarDB();
    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, date('Y-m-d H:i:s', $expiry)]);

    setcookie('remember_me', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
}

/**
 * Função para verificar cookie "lembrar-me"
 * 
 * Verifica se existe um cookie válido para autenticação automática
 * e implementa rotação de tokens para aumentar a segurança
 * 
 * @return mixed ID do usuário se o token for válido, False caso contrário
 */
function verificarCookieLembrarMe()
{
    if (isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];
        $pdo = conectarDB();

        $stmt = $pdo->prepare("
            SELECT user_id FROM remember_tokens 
            WHERE token = ? AND expires_at > NOW() 
            AND used = 0
        ");
        $stmt->execute([$token]);

        if ($result = $stmt->fetch()) {
            // Marca o token como usado (one-time use)
            $stmt = $pdo->prepare("UPDATE remember_tokens SET used = 1 WHERE token = ?");
            $stmt->execute([$token]);

            // Cria um novo token
            criarCookieLembrarMe($result['user_id']);

            return $result['user_id'];
        }
    }
    return false;
}
