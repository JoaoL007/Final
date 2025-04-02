<?php
require_once 'config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $erro = "Preencha todos os campos";
    } else {
        $pdo = conectarDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            header('Location: index.php');
            exit;
        } else {
            $erro = "Usuário ou senha inválidos";
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
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
        
        <div class="links">
            <a href="register.php">Criar conta</a>
        </div>
    </div>
</body>
</html> 