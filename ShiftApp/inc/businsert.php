<?php
include './db.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get parameters from the URL
    $name = isset($_GET['name']) ? $_GET['name'] : null;
    $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
    $lng = isset($_GET['lng']) ? $_GET['lng'] : null;

    if ($name && $lat && $lng) {
        // Query to get the id (route_id) from the autocarros table
        $sql = "SELECT id FROM AUTOCARROS WHERE name = '$name' LIMIT 1";
        $result = my_query($sql, $conn);

        if (!empty($result)) {
            $route_id = $result[0]['id']; // Get the id (route_id)

            // Insert the new record into the "detected_buses" table
            $sql = "INSERT INTO detected_buses (route_id, lat, lng) VALUES ('$route_id', '$lat', '$lng')";
            $insertResult = my_query($sql, $conn);

            if ($insertResult) {
                echo "Record inserted successfully: Route ID = $route_id, Lat = $lat, Lng = $lng";
            } else {
                echo "Failed to insert record into detected_buses.";
            }
        } else {
            echo "No matching record found in 'autocarros' for name: $name.";
        }
    } else {
        echo "Missing one or more parameters: 'name', 'lat', 'lng'.";
    }
} else {
    echo "Invalid request method. Please use GET.";
}
?>