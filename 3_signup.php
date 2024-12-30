<?php
require '0_db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // verifier les passwords 
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
        echo "<script>window.location.href = '3_signup.php';</script>";
        exit;
    }
    
    // verifier si l'email existe
    $query = "SELECT 1 FROM clients WHERE email = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        // Afficher le message d'erreur si la préparation de la requête a échoué
        echo "<script>alert('Error preparing SQL query: " . $conn->error . "');</script>";
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

     if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email or log in.');</script>";
    } else {
        // ajouter le nouveau compte à la base de données
        $query = "INSERT INTO clients (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            // afficher le message d'erreur si la préparation de la requête a échoué
            echo "<script>alert('Error preparing SQL query: " . $conn->error . "');</script>";
            exit;
        }

        $stmt->bind_param("sss", $name, $email, $password); // Lie les paramètres à l'instruction SQL et l'exécute. (sss = string, string, string) signifie que les trois paramètres sont des string. 

        if ($stmt->execute()) { // si l'instruction s'exécute avec succès
            echo "<script>alert('Account created successfully.');</script>";
        } else { 
            echo "<script>alert('Error creating account. Please try again.');</script>";
        }
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
    <title>Signup Page</title>
<link rel="icon" href="images/mercato-transparent.png" type="image/png">
</head>
<link rel="stylesheet" href="3_signup.css?<?= time(); ?>">
<body>
    <div class="container">
        <div class="left">
            <img src="images/mercato-transparent.png" style="width: 150px; height: auto;">
            <h1>Welcome to Mercato Store!</h1>
            <p>Your only place to buy talented players.</p>
            <img src="images/mercatogray.png" style="width: 200px; height: 200px;">
            </div>
        <div class="right">
            <h2>Create your account</h2>
            <form action="3_signup.php" method="POST">
                <label for="name">Full name </label>
                <input type="text" id="name" name="name" placeholder="Type your full name " required>
                <label for="email">E-mail </label>
                <input type="text" id="email" name="email" placeholder="Type your e-mail " required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Type your password" required>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Type your password again" required>
                <button type="submit">Sign Up</button>
                <p class="login">
                    Already have an account? <a href="3_login.php">Sign In</a>
                </p>
                <p class="Admin">
                    you're an admin? <a href="1_Admin.php">Sign In here</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>