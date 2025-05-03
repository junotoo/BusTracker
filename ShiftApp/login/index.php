<?php
session_start();
include '../inc/db.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $username = $_GET['email'];
    $password = $_GET['password'];

    $stmt = $conn->prepare('SELECT id, passwd, type FROM users WHERE user = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash, $type);
        $stmt->fetch();
        
        if (password_verify($password, $hash)) {
            $userId = $id;


            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["token"] = $login_token;
            //redirectLocation = isset($_GET['retlink']) ? $_GET['retlink'] : '/';
            if ($type === 1) {
                echo 4;
            }else{echo 1;}
            
        } else {
            echo 2;
            $error_message = 'Invalid password.';
        }
    } else {
        echo 3;
        $error_message = 'No account found with that username.';
    }
    
}else{
echo $_SERVER['REQUEST_METHOD'];
}
?>
