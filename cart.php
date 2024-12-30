<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: 3_login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

require('0_db_connection.php');

// Fetch cart items
$sql = "SELECT players.id, players.player_name, players.value, players.image, cart_items.quantity 
        FROM cart_items 
        JOIN players ON cart_items.player_id = players.id 
        WHERE cart_items.client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_quantity = 0;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_quantity += $row['quantity'];
}

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['value'] * $item['quantity'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
<link rel="icon" href="images/mercato-transparent.png" type="image/png">
    <link rel="stylesheet" href="cart.css?<?= time(); ?>">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo">Your Cart</div>
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

        <div class="cart-table">
            <div class="table-header">
                <div>Item</div>
                <div>Price</div>
                <div>Quantity</div>
                <div>Total Price</div>
                <div></div>
            </div>

            <?php foreach($cart_items as $item): ?>
            <div class="cart-item" data-player-id="<?php echo $item['id']; ?>">
                <div class="item-image">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>"
                        alt="<?php echo htmlspecialchars($item['player_name']); ?>">
                </div>
                <div class="item-details">
                    <h3>
                        <?php echo htmlspecialchars($item['player_name']); ?>
                    </h3>
                </div>
                <div class="price">$
                    <?php echo number_format($item['value'], 2); ?>
                </div>
                <div class="quantity-control">
                    <button class="quantity-btn decrease" <?php echo $item['quantity']==1 ? 'disabled' : '' ;
                        ?>>−</button>
                    <span class="quantity">
                        <?php echo $item['quantity']; ?>
                    </span>
                    <button class="quantity-btn increase">+</button>
                </div>
                <div class="price total-price">$
                    <?php echo number_format($item['value'] * $item['quantity'], 2); ?>
                </div>

                <button class="remove" onclick="removeItem(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 69 14" class="svgIcon bin-top">
                        <g clip-path="url(#clip0_35_24)">
                            <path fill="black"
                                d="M20.8232 2.62734L19.9948 4.21304C19.8224 4.54309 19.4808 4.75 19.1085 4.75H4.92857C2.20246 4.75 0 6.87266 0 9.5C0 12.1273 2.20246 14.25 4.92857 14.25H64.0714C66.7975 14.25 69 12.1273 69 9.5C69 6.87266 66.7975 4.75 64.0714 4.75H49.8915C49.5192 4.75 49.1776 4.54309 49.0052 4.21305L48.1768 2.62734C47.3451 1.00938 45.6355 0 43.7719 0H25.2281C23.3645 0 21.6549 1.00938 20.8232 2.62734ZM64.0023 20.0648C64.0397 19.4882 63.5822 19 63.0044 19H5.99556C5.4178 19 4.96025 19.4882 4.99766 20.0648L8.19375 69.3203C8.44018 73.0758 11.6746 76 15.5712 76H53.4288C57.3254 76 60.5598 73.0758 60.8062 69.3203L64.0023 20.0648Z">
                            </path>
                        </g>
                        <defs>
                            <clipPath id="clip0_35_24">
                                <rect fill="white" height="14" width="69"></rect>
                            </clipPath>
                        </defs>
                    </svg>

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 69 57" class="svgIcon bin-bottom">
                        <g clip-path="url(#clip0_35_22)">
                            <path fill="black"
                                d="M20.8232 -16.3727L19.9948 -14.787C19.8224 -14.4569 19.4808 -14.25 19.1085 -14.25H4.92857C2.20246 -14.25 0 -12.1273 0 -9.5C0 -6.8727 2.20246 -4.75 4.92857 -4.75H64.0714C66.7975 -4.75 69 -6.8727 69 -9.5C69 -12.1273 66.7975 -14.25 64.0714 -14.25H49.8915C49.5192 -14.25 49.1776 -14.4569 49.0052 -14.787L48.1768 -16.3727C47.3451 -17.9906 45.6355 -19 43.7719 -19H25.2281C23.3645 -19 21.6549 -17.9906 20.8232 -16.3727ZM64.0023 1.0648C64.0397 0.4882 63.5822 0 63.0044 0H5.99556C5.4178 0 4.96025 0.4882 4.99766 1.0648L8.19375 50.3203C8.44018 54.0758 11.6746 57 15.5712 57H53.4288C57.3254 57 60.5598 54.0758 60.8062 50.3203L64.0023 1.0648Z">
                            </path>
                        </g>
                        <defs>
                            <clipPath id="clip0_35_22">
                                <rect fill="white" height="57" width="69"></rect>
                            </clipPath>
                        </defs>
                    </svg>
                </button>

            </div>
            <?php endforeach; ?>
        </div>

        <div class="total-price-section">
            <span>Total Price:</span>
            <span class="total-price-value">$
                <?php echo number_format($total_price, 2); ?>
            </span>
        </div>

        <div class="cart-footer">
            <a href="store.php" class="back-link">← Back to shopping</a>
            <div class="checkout-section">
                <button class="checkout-btn">Checkout</button>
            </div>
        </div>
    </main>

    <script>
        // mis à jour du panier
        document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function () {
        const cartItem = this.closest('.cart-item');
        const playerId = cartItem.dataset.playerId;
        const quantityElement = cartItem.querySelector('.quantity');
        const priceElement = cartItem.querySelector('.price');
        const totalPriceElement = cartItem.querySelector('.total-price');
        const totalPriceValueElement = document.querySelector('.total-price-value');
        const cartCount = document.querySelector('.cart-count');
        const decreaseButton = cartItem.querySelector('.decrease');

        let quantity = parseInt(quantityElement.textContent);
        const price = parseFloat(priceElement.textContent.replace(/[^0-9.-]+/g, ''));

        quantity += this.classList.contains('increase') ? 1 : -1;

        fetch('2_update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ player_id: playerId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                quantityElement.textContent = quantity;
                totalPriceElement.textContent = `$${(price * quantity).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                cartCount.textContent = parseInt(cartCount.textContent) + (this.classList.contains('increase') ? 1 : -1);
                const currentTotalPrice = parseFloat(totalPriceValueElement.textContent.replace(/[^0-9.-]+/g, ''));
                totalPriceValueElement.textContent = `$${(currentTotalPrice + (this.classList.contains('increase') ? price : -price)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                decreaseButton.disabled = quantity <= 1;
            } else {
                alert('Failed to update quantity: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the quantity.');
        });
    });
});

        // suppression d'un article 
        document.querySelectorAll('.remove').forEach(button => {
        button.addEventListener('click', function () {
        const cartItem = this.closest('.cart-item');
        const playerId = cartItem.dataset.playerId;
        const totalPriceValueElement = document.querySelector('.total-price-value');
        const cartCount = document.querySelector('.cart-count');

        fetch('2_remove_cart_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ player_id: playerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const itemPrice = parseFloat(cartItem.querySelector('.total-price').textContent.replace(/[^0-9.-]+/g, ''));
                const currentTotalPrice = parseFloat(totalPriceValueElement.textContent.replace(/[^0-9.-]+/g, ''));
                totalPriceValueElement.textContent = `$${(currentTotalPrice - itemPrice).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                const itemQuantity = parseInt(cartItem.querySelector('.quantity').textContent);
                cartCount.textContent = parseInt(cartCount.textContent) - itemQuantity;

                cartItem.remove();
                alert('Item removed successfully');
            } else {
                alert('Failed to remove item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the item.');
        });
    });
});
   </script>
</body>
</html>