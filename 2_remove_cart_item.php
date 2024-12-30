<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['player_id'])) {
        echo json_encode(['success' => false, 'message' => 'Player ID not provided']);
        exit;
    }

    $player_id = $data['player_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "webfinal";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $sql = "DELETE FROM cart_items WHERE client_id = ? AND player_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $client_id, $player_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}