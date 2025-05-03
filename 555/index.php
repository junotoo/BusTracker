<?php
    include './inc/db.inc.php';
    include './inc/auth.inc.php';
    
    $user = loggedin($conn);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $type = $row['type'];
    $username = $row['username'];
    $bancada = $row['bancada'];
    if (!isset($bancada)&& $type ==1){
        header("Location: /bancadaID.php");
    }



    
    if ($type == 1){
            
        include 'tecnico.php';
    }else{
        include 'logistico.php';
    }

?>
