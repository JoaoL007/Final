<?php
require_once 'config.php';
requireLogin();

$pdo = conectarDB();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    
    if (!empty($senha_atual)) {
        if (password_verify($senha_atual, $user['password'])) {
            if (!empty($nova_senha)) {
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([password_hash($nova_senha, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                $mensagem = "Senha atualizada com sucesso!";
            }
        } else {
            $mensagem = "Senha atual incorreta!";
        }
    }
    
    if (!empty($nome) && !empty($email)) {
        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $_SESSION['user_id']]);
        $mensagem = "Dados atualizados com sucesso!";
        $user['nome'] = $nome;
        $user['email'] = $email;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - MyLogin System</title>
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
            background: var(--light-bg);
            color: var(--text-color);
        }

        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-brand {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card h2 {
            color: var(--primary-color);
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--accent-color);
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

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
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="landing.php" class="nav-brand">MyLogin System</a>
            <div class="nav-links">
                <a href="landing.php">Início</a>
                <a href="logout.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($mensagem): ?>
            <div class="message"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <div class="card">
            <div style="text-align: center;">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                </div>
                <h2>Bem-vindo(a), <?php echo htmlspecialchars($user['nome']); ?>!</h2>
            </div>
        </div>

        <div class="card">
            <h2>Atualizar Perfil</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <button type="submit" class="btn">Atualizar Dados</button>
            </form>
        </div>

        <div class="card">
            <h2>Alterar Senha</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="senha_atual">Senha Atual:</label>
                    <input type="password" id="senha_atual" name="senha_atual" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="form-control" required>
                </div>

                <button type="submit" class="btn">Alterar Senha</button>
            </form>
        </div>

        <div class="card">
            <h2>Informações da Conta</h2>
            <p><strong>Usuário:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Data de registro:</strong> <?php echo date('d/m/Y H:i', strtotime($user['data_registro'])); ?></p>
        </div>
    </div>
</body>
</html> 