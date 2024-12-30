<?php
require('0_db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_name = $_POST['player_name'];
    $value = $_POST['value'];
    $description = $_POST['description'];
    $position = $_POST['position'];
    $age = $_POST['age'];
    $club = $_POST['club'];
    $nationality = $_POST['nationality'];
    $image = $_FILES['image'];

    $image_name = $image['name'];
    $image_tmp_name = $image['tmp_name'];
    $image_size = $image['size'];
    $image_error = $image['error'];

    $image_ext = explode('.', $image_name);
    $image_actual_ext = strtolower(end($image_ext));

    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($image_actual_ext, $allowed)) {
        if ($image_error === 0) {
            if ($image_size < 1000000000) {
                $image_name_new = uniqid('', true) . "." . $image_actual_ext;
                $image_destination = 'images/' . $image_name_new;
                move_uploaded_file($image_tmp_name, $image_destination);

                $sql = "INSERT INTO players (player_name, value, description,position, age, club, nationality, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sississs", $player_name, $value, $description, $position, $age, $club, $nationality, $image_destination);

                if ($stmt->execute()) {
                    echo "<script>alert('Product added successfully.'); window.location.href='1_admin_dashboard.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Error adding product: " . $conn->error . "');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Your image is too big.');</script>";
            }
        } else {
            echo "<script>alert('There was an error uploading your image.');</script>";
        }
    } else {
        echo "<script>alert('You cannot upload files of this type.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <link rel="icon" href="images/mercato-transparent.png" type="image/png">
</head>
<style>
  /* Global Styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif;
  }

  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  }

  /* Header Styles */
  header {
    padding: 20px 0;
    margin-bottom: 20px;
    background: white;
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
    background: white;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  
  .logo {
    font-weight: bold;
    font-size: 40px;
    color: #333;
}

  .header-icons {
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .header-icons a {
    text-decoration: none;
    color: #666;
    transition: color 0.2s;
  }

  .header-icons a:hover {
    color: #333333;
  }

    form {
        width: 50%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        margin-top: 50px;
        border-radius: 5px;
    }

    form label {
        display: block;
        margin-top: 10px;
    }

    form input[type="text"],
    form input[type="number"],
    form textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form input[type="file"] {
        margin-top: 10px;
    }

    form button {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: none;
        background-color: #333;
        color: #fff;
        cursor: pointer;
    }
    select{
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
</style>
<body>
    <header>
        <div class="container">
        <div class="header-top">
            <div class="logo">Add Product</div>
            <div class="header-icons">
            <a href="1_admin_dashboard.php">Dashboard</a>
            <a href="1_product_add.php">Add Product</a>
            <a href="1_Admin.php">Logout</a>
            </div>
        </div>
        </div>
    </header>
    
    <form action="1_product_add.php" method="POST" enctype="multipart/form-data">

        <label for="player_name">Player Name</label>
        <input type="text" id="player_name" name="player_name" required>


        <label for="value">Value</label>
        <input type="number" id="value" name="value" required min="0" step="5000000">
        
        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="position">Position</label>
            <select id="position" name="position" required>
                <option value="gk">Goalkeeper (GK)</option>
                <option value="cb">Center Back (CB)</option>
                <option value="lb">Left Back (LB)</option>
                <option value="rb">Right Back (RB)</option>
                <option value="dmf">Defensive Midfielder (DMF)</option>
                <option value="cmf">Central Midfielder (CMF)</option>
                <option value="amf">Attacking Midfielder (AMF)</option>
                <option value="lw">Left Wing (LW)</option>
                <option value="rw">Right Wing (RW)</option>
                <option value="cf">Center Forward (CF)</option>
                <option value="ss">Second Striker (SS)</option>
            </select>

        <label for="age">Age</label>
        <input type="number" id="age" name="age" required min="0">

        <label for="club">Club</label>
        <input type="text" id="club" name="club" required>

        <label for="nationality">Nationality</label>
        <input type="text" id="nationality" name="nationality" required>

        <label for="image">Image</label>
        <input type="file" id="image" name="image" required>

        <button type="submit">Add Product</button>
    </form>
</body>