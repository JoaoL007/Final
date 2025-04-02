-- Cria o banco de dados
CREATE DATABASE IF NOT EXISTS mylogin
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mylogin;

-- Cria a tabela de usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL,
    tentativas_login INT DEFAULT 0,
    bloqueado_ate TIMESTAMP NULL,
    status ENUM('ativo', 'inativo', 'bloqueado') DEFAULT 'ativo',
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cria a tabela de tokens "lembrar-me"
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cria a tabela de logs de acesso
CREATE TABLE IF NOT EXISTS access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action ENUM('login', 'logout', 'failed_login', 'password_change', 'profile_update') NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_action (user_id, action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insere um usuário de teste
-- Senha: Teste@123
INSERT INTO users (username, password, email, nome) VALUES
('teste', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teste@teste.com', 'Usuário Teste');

-- Cria a tabela de configurações do usuário
CREATE TABLE IF NOT EXISTS user_settings (
    user_id INT PRIMARY KEY,
    theme ENUM('light', 'dark') DEFAULT 'light',
    notifications BOOLEAN DEFAULT TRUE,
    language VARCHAR(5) DEFAULT 'pt_BR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insere configurações padrão para o usuário de teste
INSERT INTO user_settings (user_id, theme, notifications, language)
SELECT id, 'light', TRUE, 'pt_BR' FROM users WHERE username = 'teste'; 