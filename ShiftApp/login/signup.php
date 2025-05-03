<?php
session_start();
include '../inc/db.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = $_GET['email'];
    $password = $_GET['password'];
    $confirm_password = $_GET['password'];

    if ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE user = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = 'Username already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (user, passwd, type) VALUES (?, ?, ?)');
            $default_type = 0; // Default user type
            $stmt->bind_param('ssi', $username, $hashed_password, $default_type);

            if ($stmt->execute()) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
}
?>
