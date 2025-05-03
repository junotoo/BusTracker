<?php
include './db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the route_id from the URL
    $route_id = isset($_GET['route_id']) ? $_GET['route_id'] : null;

    if ($route_id) {
        // Query to fetch the most recent record for the given route_id
        $sql = "SELECT
                ap.Ordem,
                p.nome,p.lat, p.lon
                FROM 
                AUTOCARROS_PARAGENS ap
                JOIN 
                PARAGENS p ON ap.paragem_id = p.id
                WHERE 
                ap.Autocarroid = $route_id  -- Replace ? with the desired AutocarroId
                ORDER BY 
                ap.Ordem;";
        $result = my_query($sql, $conn);

        if (!empty($result)) {
            echo json_encode($result); // Return the result as JSON
        } else {
            echo "No records found for route_id: $route_id.";
        }
    } else {
        echo "Missing 'route_id' parameter.";
    }
} else {
    echo "Invalid request method. Please use GET.";
}
?>