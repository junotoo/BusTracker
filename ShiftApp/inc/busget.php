<?php
include './db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the route_id from the URL
    $route_id = isset($_GET['route_id']) ? $_GET['route_id'] : null;

    if ($route_id) {
        // Query to fetch the most recent record for the given route_id
        $sql = "SELECT * FROM detected_buses WHERE route_id = '$route_id' LIMIT 1";
        $result = my_query($sql, $conn);

        if (!empty($result)) {
            $row = $result[0]; // Get the first (and only) row
            echo json_encode($row); // Return the result as JSON
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