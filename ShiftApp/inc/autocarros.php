<?php
include './db.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all "nome" fields from the "autocarros" table
    $sql = "SELECT DISTINCT name FROM AUTOCARROS";
    $result = my_query($sql, $conn);

    // Check if there are results
    if (!empty($result)) {
        foreach ($result as $row) {
            echo $row['name'].",";
        }
    } else {
        echo "No records found in the 'autocarros' table.";
    }
} else {
    echo "Invalid request method. Please use GET.";
}
?>