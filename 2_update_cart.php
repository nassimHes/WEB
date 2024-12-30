<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['player_id']) && isset($data['quantity'])) {
        $player_id = $data['player_id'];
        $quantity = $data['quantity'];
        $client_id = $_SESSION['user_id'];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "webfinal";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            echo json_encode(['success' => false, 'message' => 'Connection failed.']);
        } else {
            $update_sql = "UPDATE cart_items SET quantity = ? WHERE client_id = ? AND player_id = ?";
            $stmt = $conn->prepare($update_sql);
            if ($stmt) {
                $stmt->bind_param("iii", $quantity, $client_id, $player_id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    error_log("Failed to update quantity.");
                    echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
                }
                $stmt->close();
            } else {
                error_log("Failed to prepare statement.");
                echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
            }
            $conn->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Player ID or quantity is missing.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>