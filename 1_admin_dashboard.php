<?php
// Database connection

require('0_db_connection.php');

// Fetch players from the database
$sql = "SELECT * FROM players";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="icon" href="images/mercato-transparent.png" type="image/png">
  <link rel="stylesheet" href="1_admin_dashboard.css?v=<?php echo time(); ?>">
 
</head>
<body>
  <header>
    <div class="container">
      <div class="header-top">
        <div class="logo">Admin Dashboard</div>
        <div class="header-icons">
          <a href="1_admin_dashboard.php">Dashboard</a>
          <a href="1_product_add.php">Add Product</a>
          <a href="1_Admin.php">Logout</a>
        </div>
      </div>
    </div>
  </header>

  <button class="add-product" onclick="addProduct()">Add a Product</button>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Player_name</th>
        <th>Value</th>
        <th>Description</th>
        <th>Position</th>
        <th>Age</th>
        <th>Club</th>
        <th>Nationality</th>
        <th>Image</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["id"] . "</td>";
          echo "<td>" . $row["player_name"] . "</td>";
          echo "<td>" . $row["value"] . "</td>";
          echo "<td>" . $row["description"] . "</td>";
          echo "<td>" . $row["Position"] . "</td>";
          echo "<td>" . $row["age"] . "</td>";
          echo "<td>" . $row["club"] . "</td>";
          echo "<td>" . $row["nationality"] . "</td>";
          echo "<td>" . $row["image"] . "</td>";
          echo "<td class='action-buttons'>
              <button onclick='deletePlayer(" . $row["id"] . ")'>Delete</button>
              </td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='10'>No players found</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <script>

    // Function to delete a player
    function deletePlayer(id) {
      if (confirm('Are you sure you want to delete this player?')) {
        window.location.href = '1_delete_player.php?id=' + id;
      }
    }

    // Function to add a new product
    function addProduct() {
      window.location.href = '1_product_add.php';
    }
  </script>


</body>
</html>

<?php
$conn->close();
?>