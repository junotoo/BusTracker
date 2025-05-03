<?php
function loggedin($conn) {
    if (isset($_COOKIE['login_token'])) {
        $login_token = $_COOKIE['login_token'];
        
        $stmt = $conn->prepare("SELECT * FROM login_sessions WHERE token = ? AND expiry > NOW()");
        $stmt->bind_param("s", $login_token);
        
        $stmt->execute();
    
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            header("Location:/login");
            exit();
        }
        
        $user = $result->fetch_assoc();
        
        return $user['userId'];
    } else {
        header("Location:/login");
        exit();
    }
}
?>
