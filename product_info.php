<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webfinal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$user_id = $_SESSION['user_id'];

// Fetch total quantity from cart
$sql = "SELECT SUM(quantity) as total_quantity FROM cart_items WHERE client_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_quantity = isset($row['total_quantity']) ? $row['total_quantity'] : 0;
$stmt->close();

// Fetch product details
$product_id = $_GET['id'];
$sql = "SELECT * FROM players WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Info</title>
<link rel="icon" href="images/mercato-transparent.png" type="image/png">
  <link rel="stylesheet" href="product_info.css?<?= time(); ?>">

  <style>
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }

      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }

      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: rgb(64, 82, 65);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      animation: fadeIn 0.5s ease-in-out;
    }

    .notification.error {
      background-color: #f44336;
    }
  </style>
</head>

<body>
  <header>
    <div class="container">
      <div class="header-top">
        <div class="logo">Mercato Shop</div>
        <nav>
          <a href="store.php">Home</a>
          <a href="#">Players</a>
          <a href="#">About</a>
          <a href="#">Contact</a>
        </nav>
        <div class="header-icons">
          <button class="icon-button" aria-label="Shopping Cart" onclick="window.location.href='cart.php'">
            <svg class="icon" viewBox="0 0 24 24" stroke="currentColor" fill="none">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="cart-count">
              <?php echo $total_quantity; ?>
            </span>
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="container1">
    <div class="product-container">
      <div class="product-image">
        <img src="<?php echo htmlspecialchars($product['image']); ?>"
          alt="<?php echo htmlspecialchars($product['player_name']); ?>">
      </div>
      <div class="product-info">
        <h1>
          <?php echo htmlspecialchars($product['player_name']); ?>
        </h1>
        <p><strong>Value:</strong> $
          <?php echo number_format($product['value'], 2); ?>
        </p>
        <p><strong>Position:</strong>
          <?php echo htmlspecialchars($product['Position']); ?>
        </p>
        <p><strong>Age:</strong>
          <?php echo intval($product['age']); ?>
        </p>
        <p><strong>Nationality:</strong>
          <?php echo htmlspecialchars($product['nationality']); ?>
        </p>
        <p><strong>Club:</strong>
          <?php echo htmlspecialchars($product['club']); ?>
        </p>
        <p><strong>Description:</strong>
          <?php echo htmlspecialchars($product['description']); ?>
        </p>

        <button class="cartBtn" onclick="addToCart(<?php echo $product['id']; ?>)">
          <svg class="cart" fill="white" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z">
            </path>
          </svg>
          ADD TO CART
          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 640 512" class="product">
            <path
              d="M211.8 0c7.8 0 14.3 5.7 16.7 13.2C240.8 51.9 277.1 80 320 80s79.2-28.1 91.5-66.8C413.9 5.7 420.4 0 428.2 0h12.6c22.5 0 44.2 7.9 61.5 22.3L628.5 127.4c6.6 5.5 10.7 13.5 11.4 22.1s-2.1 17.1-7.8 23.6l-56 64c-11.4 13.1-31.2 14.6-44.6 3.5L480 197.7V448c0 35.3-28.7 64-64 64H224c-35.3 0-64-28.7-64-64V197.7l-51.5 42.9c-13.3 11.1-33.1 9.6-44.6-3.5l-56-64c-5.7-6.5-8.5-15-7.8-23.6s4.8-16.6 11.4-22.1L137.7 22.3C155 7.9 176.7 0 199.2 0h12.6z">
            </path>
          </svg>
        </button>
      </div>
    </div>
  </main>

  <script>
    function showNotification(message, isError = false) {
      const notification = document.createElement('div');
      notification.className = 'notification';
      if (isError) {
        notification.classList.add('error');
      }
      notification.textContent = message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.style.animation = 'slideOut 0.5s ease-in-out';
        notification.addEventListener('animationend', () => {
          notification.remove();
        });
      }, 3000);
    }

    function addToCart(productId) {
      fetch('2_add_to_cart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ player_id: productId })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showNotification('Player added to cart successfully.');
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            cartCount.textContent = parseInt(cartCount.textContent) + 1;
          } else {
            showNotification('Failed to add player to cart: ' + data.message, true);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('An error occurred while adding the player to the cart.', true);
        });
    }
  </script>
</body>
</html>