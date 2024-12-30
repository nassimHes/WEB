<?php
session_start();

require('0_db_connection.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT name, email, account_creation_date FROM clients WHERE id = ?");
$query->bind_param('i', $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

echo json_encode(['success' => true, 'name' => $user['name'], 'email' => $user['email'], 'account_creation_date' => $user['account_creation_date']]);
?>