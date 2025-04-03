<?php

/**
 * Página inicial do sistema MyLogin
 * Exibe a landing page com informações sobre o sistema
 * Mostra diferentes opções baseadas no estado de autenticação do usuário
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyLogin System - Sistema de Login Seguro</title>
    <!-- Estilos globais -->
    <link rel="stylesheet" href="style.css">
    <style>
        /* Variáveis de cores do tema */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-bg: #f5f5f5;
        }

        /* Estilos básicos do corpo */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--text-color);
        }

        /* Barra de navegação */
        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Container da navegação */
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logo/marca na navegação */
        .nav-brand {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Container dos links de navegação */
        .nav-links {
            display: flex;
            gap: 20px;
        }

        /* Links individuais na navegação */
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        /* Efeito hover nos links */
        .nav-links a:hover {
            background-color: var(--secondary-color);
        }

        /* Seção hero com destaque */
        .hero {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        /* Título principal na seção hero */
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        /* Descrição na seção hero */
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Seção de funcionalidades */
        .features {
            padding: 4rem 0;
            background-color: var(--light-bg);
        }

        /* Container de conteúdo centralizado */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Grid de funcionalidades */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        /* Cartão individual de funcionalidade */
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Título do cartão de funcionalidade */
        .feature-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Estilo básico de botão */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--secondary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        /* Efeito hover no botão */
        .btn:hover {
            background-color: #2980b9;
        }

        /* Variante de botão com cor de destaque */
        .btn-accent {
            background-color: var(--accent-color);
        }

        /* Efeito hover no botão de destaque */
        .btn-accent:hover {
            background-color: #c0392b;
        }

        /* Rodapé */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        /* Responsividade para dispositivos móveis */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Barra de navegação principal -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">MyLogin System</a>
            <div class="nav-links">
                <?php if (estaLogado()): // Verifica se o usuário está logado 
                ?>
                    <a href="dashboard.php">Minha Conta</a>
                    <a href="logout.php" class="btn btn-accent">Sair</a>
                <?php else: // Exibe opções para usuários não logados 
                ?>
                    <a href="login.php">Entrar</a>
                    <a href="register.php" class="btn">Criar Conta</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Seção hero com chamada principal -->
    <section class="hero">
        <div class="container">
            <h1>Sistema de Login Seguro e Moderno</h1>
            <p>Uma solução completa para autenticação de usuários com recursos avançados de segurança e uma interface intuitiva.</p>
            <?php if (!estaLogado()): // Exibe botão de call-to-action para não logados 
            ?>
                <a href="register.php" class="btn">Começar Agora</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Seção de funcionalidades -->
    <section class="features">
        <div class="container">
            <h2 style="text-align: center; color: var(--primary-color);">Recursos</h2>
            <div class="features-grid">
                <!-- Cartões de funcionalidades -->
                <div class="feature-card">
                    <h3>Segurança Avançada</h3>
                    <p>Proteção contra injeção SQL, hash seguro de senhas e validação de dados.</p>
                </div>
                <div class="feature-card">
                    <h3>Interface Moderna</h3>
                    <p>Design responsivo e intuitivo para a melhor experiência do usuário.</p>
                </div>
                <div class="feature-card">
                    <h3>Gerenciamento de Perfil</h3>
                    <p>Visualize e gerencie suas informações pessoais facilmente.</p>
                </div>
                <div class="feature-card">
                    <h3>Fácil Integração</h3>
                    <p>Sistema modular e bem documentado, pronto para ser integrado ao seu projeto.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); // Ano atual 
                        ?> MyLogin System. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>