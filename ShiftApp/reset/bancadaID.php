<?php
include './inc/db.inc.php';
include './inc/auth.inc.php';
session_start();
$i = loggedin($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bancada'])) {
    $bancada = $_POST['bancada'];

    $checkQuery = "SELECT id FROM users WHERE bancada = ? AND id != ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("si", $bancada, $i);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Erro: A bancada já está em uso por outro utilizador.');</script>";
    } else {
        $updateQuery = "UPDATE users SET bancada = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $bancada, $i);

        if ($stmt->execute()) {
            setcookie("bancada", $bancada, time() + 80000, "/");
            echo "<script>alert('Bancada definida com sucesso!');</script>";
            header("Location: /");
        } else {
            echo "<script>alert('Erro ao definir bancada: " . $stmt->error . "');</script>";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympus - Definir Bancada de Trabalho</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <form method="POST">
            <h1><img src="img/logo.png" alt="Olympus"></h1>
            <div class="input-group">
                <img src="img/search.png" alt="Ícone de pesquisa">
                <input type="text" id="bancada" name="bancada" placeholder="Insira ID Bancada" required>
            </div>
            <button type="submit">Definir</button>
        </form>
    </div>
</body>
</html>