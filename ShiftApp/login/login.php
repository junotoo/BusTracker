<?php
session_start();
include '../inc/db.inc.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT id, passwd,type FROM users WHERE user = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash,$type);
        $stmt->fetch();
        
        if (password_verify($password, $hash)) {
            $userId = $id;
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $login_token = bin2hex(random_bytes(16));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 days'));

            $stmt->bind_param('isss', $userId, $ip_address, $login_token, $expiry);
            $stmt->execute();

            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["token"] = $login_token;
            setcookie('login_token', $login_token, strtotime($expiry), '/');
            $redirectLocation = isset($_GET['retlink']) ? $_GET['retlink'] : '/';
            if ($type==1){
                header("Location: /bancadaID.php");
            }
            exit;
        } else {
            $error_message = 'Invalid password.';
        }
    } else {
        $error_message = 'No account found with that username.';
    }
}
