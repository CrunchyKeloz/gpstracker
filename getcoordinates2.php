<?php

$pgsql_server = "Endpoint";
$pgsql_user = "User";
$pgsql_password = "Password";
$pgsql_db = "DataBaseName";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get time range from user
    $startTimestamp = $_POST['startTime'];
    $endTimestamp = $_POST['endTime'];
    echo "Start Timestamp: " . $startTimestamp . "<br>";
    echo "End Timestamp: " . $endTimestamp . "<br>";

    try {
        // PostgreSQL connection
        $conn = new PDO("pgsql:host=$pgsql_server;dbname=$pgsql_db", $pgsql_user, $pgsql_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query in time range
        $query = "SELECT longitude, latitude FROM coordinates WHERE timestamp >= :start AND timestamp <= :end";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':start', $startTimestamp, PDO::PARAM_INT);
        $stmt->bindParam(':end', $endTimestamp, PDO::PARAM_INT);
        $stmt->execute();

        // Get filtered coordinates
        $coordenadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Show obtnaied coordinates
        foreach ($coordenadas as $coordenada) {
            echo "<p>Longitude: " . $coordenada['longitude'] . ", Latitude: " . $coordenada['latitude'] . "</p>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

        // Disconnect from Data Base
    $conn = null;
}
