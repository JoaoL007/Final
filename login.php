<?php
/**
 * Página de Login
 * 
 * Este arquivo implementa a autenticação de usuários com:
 * - Validação client-side usando JavaScript
 * - Validação server-side usando PHP
 * - Proteção CSRF
 * - Feedback visual para o usuário
 */

require_once 'config.php';

// Gera token CSRF para proteção contra ataques
$csrf_token = gerarCSRFToken();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validação do token CSRF
    if (!validarCSRFToken($_POST['csrf_token'] ?? '')) {
        $erro = "Erro de validação do formulário";
        logError("Tentativa de login com token CSRF inválido: " . $_SERVER['REMOTE_ADDR']);
    } else {
        $username = limparDados($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $erro = "Preencha todos os campos";
        } else {
            // Verifica tentativas de login
            $check_attempts = verificarTentativasLogin($username);
            if ($check_attempts !== true) {
                $erro = $check_attempts;
            } else {
                $pdo = conectarDB();
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nome'];
                    
                    // Registra o login bem-sucedido
                    $stmt = $pdo->prepare("
                        INSERT INTO access_logs (user_id, action, ip_address, user_agent) 
                        VALUES (?, 'login', ?, ?)
                    ");
                    $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    incrementarTentativasLogin($username);
                    $erro = "Usuário ou senha inválidos";
                    
                    // Registra a tentativa falha
                    if ($user) {
                        $stmt = $pdo->prepare("
                            INSERT INTO access_logs (user_id, action, ip_address, user_agent) 
                            VALUES (?, 'failed_login', ?, ?)
                        ");
                        $stmt->execute([$user['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyLogin System</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-bg: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 400px;
            margin: 20px;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h2 {
            color: var(--primary-color);
            margin: 0;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .error {
            background-color: #fee;
            color: var(--accent-color);
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .links a {
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .links a:hover {
            color: var(--primary-color);
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .form-control.error {
            border-color: var(--accent-color);
        }
        .field-error {
            color: var(--accent-color);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <a href="landing.php">MyLogin System</a>
        </div>
        
        <div class="header">
            <h2>Login</h2>
        </div>
        
        <?php if ($erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm" onsubmit="return validateForm()">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" class="form-control" required
                       oninput="validateField('username')" onblur="validateField('username')">
                <div id="username-error" class="field-error"></div>
            </div>
            
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" class="form-control" required
                       oninput="validateField('password')" onblur="validateField('password')">
                <div id="password-error" class="field-error"></div>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        
        <div class="links">
            <a href="register.php">Criar conta</a>
        </div>
    </div>

    <script>
    /**
     * Validação client-side dos campos do formulário
     * Fornece feedback visual imediato ao usuário
     */
    function validateField(fieldName) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.getElementById(fieldName + '-error');
        let error = '';

        switch (fieldName) {
            case 'username':
                if (field.value.length < 3) {
                    error = 'O usuário deve ter pelo menos 3 caracteres';
                }
                break;
            case 'password':
                if (field.value.length < 6) {
                    error = 'A senha deve ter pelo menos 6 caracteres';
                }
                break;
        }

        field.classList.toggle('error', error !== '');
        errorDiv.textContent = error;
        return error === '';
    }

    function validateForm() {
        let isValid = true;
        ['username', 'password'].forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });
        return isValid;
    }
    </script>
</body>
</html> 