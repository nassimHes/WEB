<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: 3_login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

require '0_db_connection.php';

// Fetch products
$sql = "SELECT id, image, player_name, value, Position, age FROM players";
$result = $conn->query($sql);

// Fetch total quantity of items in the cart
$cart_count_sql = "SELECT SUM(quantity) as total_quantity FROM cart_items WHERE client_id = ?";
$stmt = $conn->prepare($cart_count_sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$cart_count_result = $stmt->get_result();
$cart_count_row = $cart_count_result->fetch_assoc();
$total_quantity = isset($cart_count_row['total_quantity']) ? $cart_count_row['total_quantity'] : 0;

$query = "SELECT MAX(value) AS max_price FROM players";
$val = $conn->query($query);
$maxPrice = 0;

if ($val && $row = $val->fetch_assoc()) {
    $maxPrice = $row['max_price'];
}

$query = "SELECT MAX(age) AS max_age FROM players";
$val = $conn->query($query);
$maxage = 0;

if ($val && $row = $val->fetch_assoc()) {
    $maxage = $row['max_age'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mercato Shop - Your premier destination for football players">
    <title>Mercato Shop</title>
    <link rel="icon" href="images/mercato-transparent.png" type="image/png">
    <link rel="stylesheet" href="store.css?<?= time(); ?>">
</head>

<body>
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo">Mercato Shop</div>
                <nav>
                    <a href="#" class="active">Home</a>
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
                    <button class="icon-button" aria-label="User Profile" onclick="toggleProfilePopup()">
                        <svg class="icon" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                            <path
                                d="m1.5 13v1a.5.5 0 0 0 .3379.4731 18.9718 18.9718 0 0 0 6.1621 1.0269 18.9629 18.9629 0 0 0 6.1621-1.0269.5.5 0 0 0 .3379-.4731v-1a6.5083 6.5083 0 0 0 -4.461-6.1676 3.5 3.5 0 1 0 -4.078 0 6.5083 6.5083 0 0 0 -4.461 6.1676zm4-9a2.5 2.5 0 1 1 2.5 2.5 2.5026 2.5026 0 0 1 -2.5-2.5zm2.5 3.5a5.5066 5.5066 0 0 1 5.5 5.5v.6392a18.08 18.08 0 0 1 -11 0v-.6392a5.5066 5.5066 0 0 1 5.5-5.5z"
                                fill="#7D8590"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="search-bar">
                <div class="search-input">
                    <button class="icon-button search-icon" aria-label="Search">
                        <svg class="icon" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <input type="text" placeholder="Search for players..." aria-label="Search players">
                    <button class="icon-button filter-icon" aria-label="Filter" id="filter-button">
                        <svg class="icon" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="product-grid" id="product-grid">
            <?php if ($result && $result->num_rows > 0): 
                while ($row = $result->fetch_assoc()): ?>
            <div class="product-card" data-player-id="<?php echo $row['id']; ?>">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($row['image']); ?>"
                        alt="<?php echo htmlspecialchars($row['player_name']); ?>" loading="lazy">
                </div>
                    <div class="product-details">
                        <h3 class="product-title">
                            <?php echo htmlspecialchars($row['player_name']); ?>
                        </h3>
                        <div class="Value">$
                            <?php echo number_format($row['value'], 2); ?>
                        </div>
                        <div class="position">Position:
                            <?php echo htmlspecialchars($row['Position']); ?>
                        </div>
                        <div class="age">Age:
                            <?php echo intval($row['age']); ?>
                        </div>
                    </div>
                <button class="add-to-cart" aria-label="Add to cart">
                    <svg class="icon" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
                <button class="view-details"
                    onclick="window.location.href='product_info.php?id=<?php echo $row['id']; ?>'">View Details</button>
            </div>
            <?php endwhile;
            else: ?>
            <p>No players found.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Profile Pop-up -->

    <div class="popup" id="profilePopup">
        <button class="value" id="showProfileInfo">
            <svg data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path
                    d="m1.5 13v1a.5.5 0 0 0 .3379.4731 18.9718 18.9718 0 0 0 6.1621 1.0269 18.9629 18.9629 0 0 0 6.1621-1.0269.5.5 0 0 0 .3379-.4731v-1a6.5083 6.5083 0 0 0 -4.461-6.1676 3.5 3.5 0 1 0 -4.078 0 6.5083 6.5083 0 0 0 -4.461 6.1676zm4-9a2.5 2.5 0 1 1 2.5 2.5 2.5026 2.5026 0 0 1 -2.5-2.5zm2.5 3.5a5.5066 5.5066 0 0 1 5.5 5.5v.6392a18.08 18.08 0 0 1 -11 0v-.6392a5.5066 5.5066 0 0 1 5.5-5.5z"
                    fill="#7D8590"></path>
            </svg>
            Account info
        </button>
        <button class="value" id="changeEmail">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16v16H4z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            change email
        </button>
        <button class="value" id="logOut">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Log Out
        </button>
        <button class="value" id="deleteAccount">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6l-2 14H7L5 6"></path>
                <path d="M10 11v6"></path>
                <path d="M14 11v6"></path>
                <path d="M5 6l1-3h12l1 3"></path>
            </svg>
            Delete Account
        </button>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Filter Popup -->
<div id="filter-popup" class="popup">
    <div class="popup-content">
        <span class="close" id="close">&times;</span>
        <h2>Filter Players</h2>
        <form id="filter-form">
            <label for="min-price">Min Price:</label>
            <input type="number" id="min-price" name="min-price" min="0" max="<?php echo $maxPrice; ?>" value="0" step="5000000">

            <label for="max-price">Max Price:</label>
            <input type="number" id="max-price" name="max-price" min="0" max="<?php echo $maxPrice; ?>" step="5000000">

            <label for="min-age">Min Age:</label>
            <input type="number" id="min-age" name="min-age" min="0" max="<?php echo $maxage; ?>" value="0" >

            <label for="max-age">Max Age:</label>
            <input type="number" id="max-age" name="max-age" min="0" max="<?php echo $maxage; ?>">

            <label for="position">Position:</label>
            <select id="position" name="position">
                <option value="">Select Position</option>
                <option value="GK">GK</option>
                <option value="CB">CB</option>
                <option value="LB">LB</option>
                <option value="RB">RB</option>
                <option value="CMF">CMF</option>
                <option value="DMF">DMF</option>
                <option value="AMF">AMF</option>
                <option value="CF">CF</option>
                <option value="LW">LW</option>
                <option value="RW">RW</option>
                <option value="SS">SS</option>
            </select>

            <button type="submit">Apply Filters</button>
        </form>
    </div>
</div>

    <script>
        // fonction de recherche
        document.querySelector('.search-input input').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase().trim();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(function (card) {
                const playerName = card.querySelector('.product-title').textContent.toLowerCase();
                
                // Search by player name
                if (playerName.includes(searchValue)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        // fonction d'ajout au panier
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const playerCard = this.closest('.product-card');
                const playerId = playerCard.dataset.playerId;

                console.log('Add to cart clicked for player ID:', playerId);

                fetch('2_add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ player_id: playerId })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response from server:', data);
                        if (data.success) {
                            alert('Player added to cart successfully.');
                            // Update cart count
                            const cartCount = document.querySelector('.cart-count');
                            cartCount.textContent = parseInt(cartCount.textContent) + 1;
                        } else {
                            alert('Failed to add player to cart: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while adding the player to the cart.', 'error');
                    });
            });
        });
        
        const style = document.createElement('style');
        style.textContent = `
        #profilePopup {
            display: none;
            position: fixed;
            top: 60px;
            right: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 20px;
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            transform: translateY(-10px);
        }
        #profilePopup.show {
            opacity: 1;
            transform: translateY(0);
        }
    `;
        document.head.appendChild(style);

        // fonction pour afficher le popup de profil
        function toggleProfilePopup() {
            const profilePopup = document.getElementById('profilePopup');
            if (profilePopup.classList.contains('show')) {
                profilePopup.classList.remove('show');
                 setTimeout(() => {
                     profilePopup.style.display = 'none';
                 }, 300);
            } else {
                profilePopup.style.display = 'flex';
                 setTimeout(() => {
                     profilePopup.classList.add('show');
                 }, 10);
            }
        }
        // Get modal elements
        const modal = document.getElementById('myModal');
        const modalBody = document.getElementById('modal-body');
        const span = document.getElementsByClassName('close')[0];
        // Show modal
        function showModal(content) {
            modalBody.innerHTML = content;
            modal.style.display = 'block';
        }
        // Close modal
        span.onclick = function () {
            modal.style.display = 'none';
        }
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        // Show profile info
        document.getElementById('showProfileInfo').addEventListener('click', function () {
            fetch('2_profile_info.php')
                .then(response => response.json())
                .then(data => {
                    const content = `<p>Name: ${data.name}</p><p>Email: ${data.email}</p><p>Account created: ${data.account_creation_date}</p>`;
                    showModal(content);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching profile info.');
                });
        });

        // Log Out functionality
        document.getElementById('logOut').addEventListener('click', function () {
            const content = `
        <p>Are you sure you want to log out?</p>
        <div class="button-group">
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    `;
            showModal(content);

            document.getElementById('confirmLogout').addEventListener('click', function () {
                fetch('2_logout.php')
                    .then(response => response.json())
                    .then(data => {
                        alert(data.success ? 'Logged out successfully.' : 'Failed to log out: ' + data.message);
                        if (data.success) window.location.href = '3_login.php';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while logging out.');
                    });
            });

            document.getElementById('cancelLogout').addEventListener('click', function () {
                modal.style.display = 'none';
            });
        });

        // Change Email functionality
        document.getElementById('changeEmail').addEventListener('click', function () {
            const content = `
        <form id="changeEmailForm">
            <label for="newEmail">New Email:</label>
            <input type="email" id="newEmail" name="newEmail" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Change Email</button>
        </form>
    `;
            showModal(content);

            document.getElementById('changeEmailForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch('2_change_email.php', {
                    method: 'POST',
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.success ? 'Email changed successfully.' : 'Failed to change email: ' + data.message);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while changing the email.');
                    });
            });
        });

        // Delete account
        document.getElementById('deleteAccount').addEventListener('click', function () {
            const content = `
    <form id="deleteAccountForm">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Delete Account</button>
    </form>
    `;
            showModal(content);

            document.getElementById('deleteAccountForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);

                if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                    fetch('2_delete_account.php', {
                        method: 'POST',
                        body: JSON.stringify(Object.fromEntries(formData))
                    })
                        .then(response => response.text()) // Get the full response text
                        .then(text => {
                            console.log(text); // Log the response text for debugging
                            try {
                                const data = JSON.parse(text); // Parse the JSON
                                alert(data.success ? 'Account deleted successfully.' : 'Failed to delete account: ' + data.message);
                                if (data.success) window.location.href = '3_signup.php';
                            } catch (e) {
                                console.error('Error parsing JSON:', e);
                                alert('An error occurred while deleting the account.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the account.');
                        });
                }
            });
        });

        // filter popup
// Open and close the filter popup
document.getElementById('filter-button').onclick = () => document.getElementById('filter-popup').style.display = 'block';
document.getElementById('close').onclick = () => document.getElementById('filter-popup').style.display = 'none';

// Submit the filter form via AJAX
document.getElementById('filter-form').onsubmit = event => {
    event.preventDefault(); // Prevent page reload

    // Collect form data and validate
    const formData = new FormData(event.target);
    const minPrice = parseInt(formData.get('min-price')) || 0;
    const maxPrice = parseInt(formData.get('max-price')) || Infinity;
    const minAge = parseInt(formData.get('min-age')) || 0;
    const maxAge = parseInt(formData.get('max-age')) || Infinity;

    if (minPrice > maxPrice || minAge > maxAge) {
        alert('Max values cannot be less than Min values.');
        return;
    }

    // Send AJAX request to filter players
    fetch('2_filter.php?' + new URLSearchParams(formData))
        .then(response => response.ok ? response.json() : Promise.reject('Error: ' + response.status))
        .then(players => {
            const productGrid = document.getElementById('product-grid');
            productGrid.innerHTML = players.length
                ? players.map(player => `
                    <div class="product-card">
                        <div class="product-image"><img src="${player.image}" alt="${player.player_name}" loading="lazy"></div>
                        <div class="product-details">
                            <h3 class="product-title">${player.player_name}</h3>
                            <div class="value">$${player.value}</div>
                            <div class="position">Position: ${player.position}</div>
                            <div class="age">Age: ${player.age}</div>
                        </div>
                    </div>`).join('')
                : '<p>No players found.</p>';
        })
        .catch(error => {
            console.error(error);
            alert('An error occurred while filtering players.');
        });
};

    </script>
</body>
</html>