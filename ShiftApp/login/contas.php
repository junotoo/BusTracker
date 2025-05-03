<?php
session_start();
include '../inc/db.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'Todos os campos são obrigatórios!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'As senhas não coincidem!';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error_message = 'Nome de usuário já existe!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username, passwd) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $hashed_password);
            if ($stmt->execute()) {
                header('Location: /login.php?signup=success');
                exit;
            } else {
                $error_message = 'Erro ao criar conta!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Criar Conta</title>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Criar Conta</h2>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST">
                <label for="username">Nome de Usuário:</label><br>
                <input type="text" id="username" name="username" required><br>
                <label for="password">Senha:</label><br>
                <input type="password" id="password" name="password" required><br>
                <label for="confirm_password">Confirmar Senha:</label><br>
                <input type="password" id="confirm_password" name="confirm_password" required><br><br>
                <button class="btn btn-primary" type="submit">Criar Conta</button>
            </form>
            <a href="/login.php" class="btn btn-secondary">Já tem uma conta? Entrar</a>
        </div>
    </div>
    <script src="../js/jquery-3.7.1.min.js"></script>
</body>
</html>
