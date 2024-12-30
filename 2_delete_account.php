<?php
session_start();

require('0_db_connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$password = $data['password'];
$user_id = $_SESSION['user_id'];

// Fetch user password
$query = $conn->prepare("SELECT password FROM clients WHERE id = ?");
$query->bind_param('i', $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

if ($password === $user['password']) {
    // supprimer les articles du panier
    $delete_cart_query = $conn->prepare("DELETE FROM cart_items WHERE client_id = ?");
    $delete_cart_query->bind_param('i', $user_id);
    $delete_cart_query->execute();

    // supprimer le compte
    $delete_query = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $delete_query->bind_param('i', $user_id);
    $delete_query->execute();

    session_destroy();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
}
?>