<?php
/**
 * @file login.php
 * @brief Aesthetic Login page for the geographic information system.
 */
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/auth.php';

// Se já estiver autenticado, redireciona para o mapa
if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha ambos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, senha, tipo FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            // Autenticação com sucesso
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo']; // 'admin' ou 'normal'
            header("Location: index.php");
            exit;
        } else {
            $erro = "Email ou palavra-passe incorretos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Localização</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fa-solid fa-map-location-dot"></i> GeoDados</h2>
            <p>Faça login para gerir locais</p>
        </div>
        
        <?php if ($erro): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="input-group">
                <label for="email">E-mail</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="O seu e-mail" required autofocus>
                </div>
            </div>
            
            <div class="input-group">
                <label for="senha">Palavra-passe</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="senha" name="senha" placeholder="A sua palavra-passe" required>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                Entrar <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>

        <div class="login-footer">
            <p>Acesso Restrito</p>
        </div>
    </div>
</body>
</html>
