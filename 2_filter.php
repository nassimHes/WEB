<?php
// Set the Content-Type to JSON
header('Content-Type: application/json');

// Include the database connection file
require_once '0_db_connection.php';

// Start with a basic SQL query to select all players
$query = "SELECT * FROM players";

// Check and apply each filter condition manually
if (!empty($_GET['min-price'])) {
    $minPrice = intval($_GET['min-price']);
    $query .= " WHERE value >= $minPrice";
}

if (!empty($_GET['max-price'])) {
    $maxPrice = intval($_GET['max-price']);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND value <= $maxPrice";
    } else {
        $query .= " WHERE value <= $maxPrice";
    }
}

if (!empty($_GET['min-age'])) {
    $minAge = intval($_GET['min-age']);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND age >= $minAge";
    } else {
        $query .= " WHERE age >= $minAge";
    }
}

if (!empty($_GET['max-age'])) {
    $maxAge = intval($_GET['max-age']);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND age <= $maxAge";
    } else {
        $query .= " WHERE age <= $maxAge";
    }
}

if (!empty($_GET['position'])) {
    $position = $conn->real_escape_string($_GET['position']);
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND position = '$position'";
    } else {
        $query .= " WHERE position = '$position'";
    }
}

// Execute the query
$result = $conn->query($query);

// Fetch the results
$players = [];
while ($row = $result->fetch_assoc()) {
    $players[] = $row;
}

// Output the results as JSON
echo json_encode($players);
?>