<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Signin</title>
</head>
<body class="auth-page-body">
<img src="../assets/images/1688361055amazon-logo-png.png" class="auth-amazon-logo">
    <div class="input-container">
        <h2 class="header-label-auth">Create Account</h2>
        <form action="../controller/signup_process.php" method="POST">
        <input type="hidden" name="role" value="customer">

            <label for="username" class="label-input">Your Name</label>
            <input type="text" name="username" class="input-field" required><br>

            <label for="email" class="label-input">Email</label>
            <input type="email" name="email" class="input-field" required><br>
            
            <label for="password" class="label-input">Password</label> 
            <input type="password" name="password" class="input-field" required><br>

            <label for="password" class="label-input">Re-Enter Password</label> 
            <input type="password" name="re-enter-password" class="input-field" required><br>
            
            <button type="submit" class="submit-btn">Continue</button>
            <?php
                if(isset($_GET['error'])){
                    if($_GET['error'] == 'invalid_email'){
                        echo "<p class='error-message'>Invalid email format</p>";
                    } else if($_GET['error'] == 'password_mismatch'){
                        echo "<p class='error-message'>Passwords do not match</p>";
                    } else if($_GET['error'] == 'username_too_short'){
                        echo "<p class='error-message'>Username must be at least 3 characters long</p>";
                    } else if($_GET['error'] == 'password_too_short'){
                        echo "<p class='error-message'>Password must be at least 8 characters long</p>";
                    } else if($_GET['error'] == 'signup_failed'){
                        echo "<p class='error-message'>account already exists</p>";
                    }
                }
            ?>
        </form>
    </div>
    <p class="or-label">Already have an account?</p>
    <div class="swap-auth-container">
        <a href="signin.php" class="swap-auth-button">Signin your Amazon account</a>
    </div>
</body>
</html>