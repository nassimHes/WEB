<?php

require('0_db_connection.php');

if (isset($_GET['id'])) {
    $player_id = $_GET['id']; 

    // Delete player from cart_items and players
    $sql_cart = "DELETE FROM cart_items WHERE player_id = $player_id";
    $sql_player = "DELETE FROM players WHERE id = $player_id";

    if ($conn->query($sql_cart) === TRUE && $conn->query($sql_player) === TRUE) {
        echo "<script>alert('Player deleted successfully.'); window.location.href='1_admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting player: " . $conn->error . "');</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('No player ID provided.');</script>";
}
?>
