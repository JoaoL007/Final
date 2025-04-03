<?php
require_once 'config.php';
requireLogin();

$pdo = conectarDB();

// Verifica se a coluna foto_perfil já existe na tabela users
$column_exists = false;
try {
    $stmt = $pdo->prepare("SELECT foto_perfil FROM users LIMIT 1");
    $stmt->execute();
    $column_exists = true;
} catch (PDOException $e) {
    // A coluna não existe
    $column_exists = false;
}

// Adiciona a coluna foto_perfil se ela não existir
if (!$column_exists) {
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL");
        $column_exists = true;
    } catch (PDOException $e) {
        // Se ocorrer um erro ao adicionar a coluna, apenas registra
        error_log("Não foi possível adicionar a coluna foto_perfil: " . $e->getMessage());
    }
}

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
        // Verifica se há upload de foto
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0 && $column_exists) {
            $foto = $_FILES['foto_perfil'];
            $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $permitidos = ['jpg', 'jpeg', 'png'];

            if (in_array(strtolower($ext), $permitidos)) {
                $nome_arquivo = 'uploads/perfil_' . $_SESSION['user_id'] . '.' . $ext;
                $diretorio = dirname(__FILE__) . '/uploads/';

                // Cria o diretório se não existir
                if (!is_dir($diretorio)) {
                    mkdir($diretorio, 0755, true);
                }

                if (move_uploaded_file($foto['tmp_name'], dirname(__FILE__) . '/' . $nome_arquivo)) {
                    if ($column_exists) {
                        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, foto_perfil = ? WHERE id = ?");
                        $stmt->execute([$nome, $email, $nome_arquivo, $_SESSION['user_id']]);
                        $user['foto_perfil'] = $nome_arquivo;
                    } else {
                        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
                        $stmt->execute([$nome, $email, $_SESSION['user_id']]);
                    }
                } else {
                    $mensagem = "Erro ao salvar a foto de perfil.";
                    $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
                    $stmt->execute([$nome, $email, $_SESSION['user_id']]);
                }
            } else {
                $mensagem = "Formato de arquivo não permitido. Use JPG, JPEG ou PNG.";
                $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
                $stmt->execute([$nome, $email, $_SESSION['user_id']]);
            }
        } else if (isset($_POST['remover_foto']) && $_POST['remover_foto'] == '1' && $column_exists) {
            // Remover foto de perfil
            if (isset($user['foto_perfil']) && !empty($user['foto_perfil']) && file_exists($user['foto_perfil'])) {
                // Tenta remover o arquivo físico
                @unlink($user['foto_perfil']);
            }

            // Atualiza o banco de dados para remover a referência
            $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, foto_perfil = NULL WHERE id = ?");
            $stmt->execute([$nome, $email, $_SESSION['user_id']]);
            $user['foto_perfil'] = null;
            $mensagem = "Foto de perfil removida com sucesso!";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $_SESSION['user_id']]);
        }

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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">MyLogin System</a>
            <div class="nav-links">
                <a href="index.php">Início</a>
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
                    <?php if (isset($user['foto_perfil']) && !empty($user['foto_perfil']) && file_exists($user['foto_perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <h2>Bem-vindo(a), <?php echo htmlspecialchars($user['nome']); ?>!</h2>
            </div>
        </div>

        <div class="card">
            <h2>Atualizar Perfil</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="foto_perfil">Foto de Perfil:</label>
                    <?php if (isset($user['foto_perfil']) && !empty($user['foto_perfil']) && file_exists($user['foto_perfil'])): ?>
                        <div class="current-photo">
                            <img src="<?php echo htmlspecialchars($user['foto_perfil']); ?>" alt="Foto atual">
                            <p style="margin: 5px 0;">Foto atual</p>
                            <label class="remove-photo-container">
                                <input type="checkbox" name="remover_foto" value="1" id="remover_foto">
                                <span>Remover foto atual</span>
                            </label>
                        </div>
                    <?php endif; ?>
                    <p style="margin-bottom: 5px; font-size: 0.9em;">Para atualizar sua foto, selecione uma nova imagem abaixo:</p>
                    <input type="file" id="foto_perfil" name="foto_perfil" class="form-control">
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