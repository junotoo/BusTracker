<?php
include('./db.inc.php');

// --- Funções auxiliares ---
function haversine($lat1, $lon1, $lat2, $lon2) {
    $R = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c;
}

function avgDistanceToRoute($clusterPoints, $routePoints) {
    $total = 0;
    foreach ($clusterPoints as $p) {
        $minDist = PHP_INT_MAX;
        foreach ($routePoints as $r) {
            $d = haversine($p['lat'], $p['lng'], $r['lat'], $r['lng']);
            if ($d < $minDist) $minDist = $d;
        }
        $total += $minDist;
    }
    return $total / count($clusterPoints);
}

// --- 1. Buscar posições recentes ---
$sql = "SELECT * FROM user_locations WHERE timestamp >= NOW() - INTERVAL 60 SECOND";
$locations = my_query($sql, $conn);

// --- 2. Agrupar clusters ---
$visited = [];
$clusters = [];

foreach ($locations as $i => $loc1) {
    if (in_array($i, $visited)) continue;
    $cluster = [$loc1];
    $visited[] = $i;

    foreach ($locations as $j => $loc2) {
        if ($i === $j || in_array($j, $visited)) continue;
        if (haversine($loc1['lat'], $loc1['lng'], $loc2['lat'], $loc2['lng']) <= 50) {
            $cluster[] = $loc2;
            $visited[] = $j;
        }
    }

    // Verifica se tem ≥ 7 utilizadores únicos
    $userIds = array_unique(array_column($cluster, 'uid'));
    if (count($userIds) >= 1) {
        // Calcular velocidade média
        $speeds = array_column($cluster, 'speed');
        $avgSpeed = array_sum($speeds) / count($speeds);

        if ($avgSpeed >= 5.56) { // ≥ 20 km/h
            $clusters[] = [
                'users' => $userIds,
                'points' => $cluster,
                'avg_speed' => $avgSpeed,
                'center_lat' => $loc1['lat'],
                'center_lng' => $loc1['lng']
            ];
        }
    }
}

// --- 3. Comparar com rotas ---
$sql = "SELECT DISTINCT Autocarroid FROM AUTOCARROS_PARAGENS"
$routes = my_query($sql, $conn);

foreach ($clusters as $cluster) {
    $bestMatch = null;
    $bestDist = PHP_INT_MAX;

    foreach ($routes as $route) {
        $route_id = $route['Autocarroid'];
        $sql = "SELECT lat, lng FROM AUTOCARROS_PARAGENS WHERE Autocarroid = '$route_id' ORDER BY Ordem ASC";
        $routePoints = my_query($sql, $conn);

        $avgDist = avgDistanceToRoute($cluster['points'], $routePoints);
        if ($avgDist < $bestDist) {
            $bestDist = $avgDist;
            $bestMatch = $route_id;
        }
    }

    if ($bestDist <= 50) {
        // --- 4. Guardar autocarro detetado ---
        $sql = "
            INSERT INTO detected_buses (route_id, lat, lng, speed, updated_at)
            VALUES ('{$bestMatch}', '{$cluster['center_lat']}', '{$cluster['center_lng']}', '{$cluster['avg_speed']}', NOW())
            ON DUPLICATE KEY UPDATE
                lat = VALUES(lat),
                lng = VALUES(lng),
                speed = VALUES(speed),
                updated_at = NOW()
        ";
        my_query($sql, $conn);
        echo "Autocarro identificado (rota $bestMatch) a " . round($cluster['avg_speed'] * 3.6, 1) . " km/h\n";
    }
}
?>