<?php
session_start();

require('0_db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Préparer la requête SQL pour vérifier l'email
    $emailCheckSql = "SELECT id, password FROM clients WHERE email = ?";
    $stmt = $conn->prepare($emailCheckSql);// Préparer la déclaration SQL
    $stmt->bind_param("s", $email);// Lier le paramètre email à la déclaration préparée
    $stmt->execute();// Exécuter la déclaration préparée
    $result = $stmt->get_result();// Obtenir le résultat de l'exécution de la déclaration

    if ($result->num_rows > 0) {
        // Email existe, verifier le password
        $row = $result->fetch_assoc();
        if ($row['password'] === $password) { // Comparer les password
            $_SESSION['user_id'] = $row['id']; // enregistrer l'id de l'utilisateur dans la session
            echo "<script>
                alert('Sign-In Successful!');
                window.location.href = 'store.php';
            </script>";
        } else {
            // password Incorrect
            echo "<script>
                alert('Incorrect password! Please try again.');
                window.location.href = '3_login.php';
            </script>";
        }
    } else {
        // Email not found
        echo "<script>
            alert('Email not found! Please check your email or create an account.');
            window.location.href = '3_login.php';
        </script>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
<link rel="icon" href="images/mercato-transparent.png" type="image/png">
    <link rel="stylesheet" href="3_login.css?<?= time(); ?>">
</head>
<body>
    <div class="container">
        <div class="left">
        <img src="images/mercato-transparent.png" style="width: 150px; height: auto;">
            <h1>Welcome to Mercato Store!</h1>
            <p>Your only place to buy talented players.</p>
            <img src="images/mercatogray.png" style="width: 200px; height: 200px;">
            </div>
        <div class="right">
            <h2>HELLO THERE!</h2>
            <p>One step away to acces the store!</p>
            <form action="3_login.php" method="POST">
                <label for="email">E-mail </label>
                <input type="text" id="email" name="email" placeholder="Type your e-mail " required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Type your password" required>
            
                <button type="submit">Sign In</button>

                <p class="signup">
                    Don't have an account? <a href="3_signup.php">Sign Up</a>
                </p>
                <p class="Admin">
                    you're an admin? <a href="1_Admin.php">Sign In here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>