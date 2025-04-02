<?php
require_once 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $nome = $_POST['nome'] ?? '';
    
    if (empty($username) || empty($password) || empty($email) || empty($nome)) {
        $erro = "Preencha todos os campos";
    } else {
        $pdo = conectarDB();
        
        // Verifica se usuário já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $erro = "Usuário ou email já cadastrado";
        } else {
            // Insere novo usuário
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, nome) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email, $nome])) {
                $sucesso = "Cadastro realizado com sucesso! <a href='login.php'>Faça login</a>";
            } else {
                $erro = "Erro ao cadastrar usuário";
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
    <title>Registro - MyLogin System</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-bg: #f5f5f5;
            --success-color: #27ae60;
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

        .success {
            background-color: #e8f6ef;
            color: var(--success-color);
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .success a {
            color: var(--success-color);
            text-decoration: underline;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <a href="landing.php">MyLogin System</a>
        </div>
        
        <div class="header">
            <h2>Registro</h2>
        </div>
        
        <?php if ($erro): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>
        
        <?php if ($sucesso): ?>
            <div class="success"><?php echo $sucesso; ?></div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                
                <button type="submit" class="btn">Registrar</button>
            </form>
        <?php endif; ?>
        
        <div class="links">
            <a href="login.php">Já tem uma conta? Faça login</a>
        </div>
    </div>
</body>
</html> 