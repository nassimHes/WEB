<?php
session_start();
require('0_db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$email = $data['newEmail'];
$password = $data['password'];
$client_id = $_SESSION['user_id'];

// Fetch the current password from the database
$stmt = $conn->prepare("SELECT password FROM clients WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && $password === $row['password']) {
    // Update the email
    $update_stmt = $conn->prepare("UPDATE clients SET email = ? WHERE id = ?");
    $update_stmt->bind_param("si", $email, $client_id);
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update email.']);
    }
    $update_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => $row ? 'Incorrect password' : 'User not found']);
}

$stmt->close();
$conn->close();
?>