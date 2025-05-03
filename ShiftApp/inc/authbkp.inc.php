<?php
session_start();
if (isset($_COOKIE['userinfo'])) {
    $userinfo = unserialize($_COOKIE['userinfo']);
    $username = $userinfo['username'];
    $password = $userinfo['password'];
    $stmt = $conn->prepare('SELECT id, passwd, type FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash, $type);
        $stmt->fetch();
        if (password_verify($password, $hash)) {

}else{
    echo "password incorreta";
    header('Location: /login');
  }
  } else {
    header('Location: /login');
    exit;
  }
  }else {
    session_start();
    header('Location: /login');
    exit;
  }

?>
