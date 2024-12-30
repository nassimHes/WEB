<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['player_id'])) {
    echo json_encode(['success' => false, 'message' => 'Player ID is missing.']);
    exit;
}

$player_id = $data['player_id'];
$client_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webfinal";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Check if the player is already in the cart
    $stmt = $conn->prepare("SELECT quantity FROM cart_items WHERE client_id = ? AND player_id = ?");
    $stmt->bind_param("ii", $client_id, $player_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if the player is already in the cart
        $new_quantity = $result->fetch_assoc()['quantity'] + 1;
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE client_id = ? AND player_id = ?");
        $stmt->bind_param("iii", $new_quantity, $client_id, $player_id);
    } else {
        // Insert new entry if the player is not in the cart
        $stmt = $conn->prepare("INSERT INTO cart_items (client_id, player_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $client_id, $player_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Database operation failed.");
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>