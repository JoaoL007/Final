<?php
/**
 * Arquivo de configuração principal
 * Contém todas as configurações do sistema e funções auxiliares
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mylogin');

// Configurações de segurança
define('MAX_LOGIN_ATTEMPTS', 3);          // Máximo de tentativas de login
define('LOGIN_TIMEOUT', 900);             // Tempo de bloqueio após tentativas (15 minutos)
define('PASSWORD_MIN_LENGTH', 8);         // Tamanho mínimo da senha
define('SESSION_LIFETIME', 3600);         // Duração da sessão (1 hora)
define('REMEMBER_ME_DURATION', 2592000);  // Duração do cookie "lembrar-me" (30 dias)

// Configurações de sessão
ini_set('session.cookie_httponly', 1);    // Previne acesso via JavaScript
ini_set('session.use_only_cookies', 1);   // Força uso apenas de cookies
ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Cookie seguro em HTTPS

// Inicia a sessão
session_start();

// Função para conectar ao banco de dados
function conectarDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
        return $pdo;
    } catch(PDOException $e) {
        logError('Database connection failed: ' . $e->getMessage());
        die("Erro na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
    }
}

// Função para verificar se está logado
function estaLogado() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Função para redirecionar se não estiver logado
function requireLogin() {
    if (!estaLogado()) {
        header('Location: login.php');
        exit;
    }
}

// Função para validar força da senha
function validarSenha($senha) {
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

// Função para gerar token CSRF
function gerarCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para validar token CSRF
function validarCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Função para registrar erros em log
function logError($mensagem) {
    $data = date('Y-m-d H:i:s');
    $log = "[{$data}] {$mensagem}\n";
    file_put_contents('error.log', $log, FILE_APPEND);
}

// Função para limpar dados de entrada
function limparDados($dados) {
    if (is_array($dados)) {
        return array_map('limparDados', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

// Função para verificar tentativas de login
function verificarTentativasLogin($username) {
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
        return "Muitas tentativas de login. Tente novamente em " . ceil($timeLeft/60) . " minutos.";
    }

    return true;
}

// Função para incrementar tentativas de login
function incrementarTentativasLogin($username) {
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = [
            'count' => 0,
            'time' => time()
        ];
    }
    $_SESSION['login_attempts'][$username]['count']++;
}

// Função para criar cookie "lembrar-me"
function criarCookieLembrarMe($userId) {
    $token = bin2hex(random_bytes(32));
    $expiry = time() + REMEMBER_ME_DURATION;
    
    $pdo = conectarDB();
    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $token, date('Y-m-d H:i:s', $expiry)]);
    
    setcookie('remember_me', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
}

// Função para verificar cookie "lembrar-me"
function verificarCookieLembrarMe() {
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