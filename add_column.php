<?php
require_once 'config.php';

try {
    // Conecta ao banco de dados
    $pdo = conectarDB();

    // Verifica se a coluna já existe
    $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'foto_perfil'");
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Adiciona a coluna se não existir
        $pdo->exec("ALTER TABLE users ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL");
        echo "Coluna foto_perfil adicionada com sucesso!";
    } else {
        echo "A coluna foto_perfil já existe na tabela users.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
