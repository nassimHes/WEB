<?php

require('0_db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verification de l'existence de l'email
    $emailCheckSql = "SELECT mdp FROM admin WHERE email = '$email'";
    $emailResult = $conn->query($emailCheckSql);

    if ($emailResult->num_rows > 0) {
        // L'email existe, on verifie le mot de passe
        $row = $emailResult->fetch_assoc();
        if ($row['mdp'] === $password) {
            // Connexion reussie
            echo "<script>
                alert('Admin Sign-In Successful!');
                window.location.href = '1_admin_dashboard.php';
            </script>";
        } else {
            // Mot de passe incorrect
            echo "<script>
                alert('Incorrect password! Please try again.');
                window.location.href = '1_Admin.php';
            </script>";
        }
    } else {
        // L'email n'existe pas
        echo "<script>
            alert('Email not found! Please check your email or contact the administrator.');
            window.location.href = '1_Admin.php';
        </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign-In Page</title>
<link rel="icon" href="images/mercato-transparent.png" type="image/png">

    <link rel="stylesheet" href="1_Admin.css?<?= time(); ?>">
</head>
<body>
    <div class="container">
        <div class="left">
        <img src="images/mercato-transparent.png" style="width: 150px; height: auto;">
        <h1>Hello brother!</h1>
            <h3>Just enter the code and fill the form to join us!</h3>
                <img src="images/mercatogray.png" style="width: 200px; height: 200px;">
        </div>
        <div class="right">
            <h2>Admin Sign In</h2>
            <form action="1_Admin.php" method="POST">

                <label for="code">Admin Code</label>
                <input type="text" id="code" name="code" placeholder="Type admin code" required oninput="checkCode()">
                <p class="Admin" id="loginback">
                    back to login page <a href="3_login.php">client login</a>
                </p>
                
                <!-- formulaire Admin caché -->
                <div id="form-fields" class="hidden">
                    <label for="email">E-mail</label>
                    <input type="text" id="email" name="email" placeholder="Type your e-mail" required>
                    
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Type your password" required>
                    
                    <button type="submit">Sign In</button>
                    <p class="client">
                        back to login page <a href="3_login.php">client login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
            // Fonction pour vérifier le code
        function checkCode() {
            const codeInput = document.getElementById('code').value;
            const formFields = document.getElementById('form-fields');
            const loginback = document.getElementById('loginback');
            
            // si le code est correct, on affiche le formulaire
            if (codeInput.trim() == '2024') {
                formFields.classList.remove('hidden');
                formFields.classList.add('visible');
                loginback.classList.add('hidden');
            } else { 
                formFields.classList.remove('visible');
                formFields.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
