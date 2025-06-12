<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Signin</title>
</head>
<body class="auth-page-body">
    <img src="" class="auth-amazon-logo">
    <div class="input-container">
        <h2 class="header-label-auth">Sign in Admin</h2>
        <form action="../controller/signin_admin.php" method="POST">
            <input type="hidden" name="role" value="admin">

            <label for="email" class="label-input">Email</label>
            <input type="email" name="email" class="input-field" required><br>
            
            <label for="password" class="label-input" required>Password</label> 
            <input type="password" name="password" class="input-field"><br>
            
            <button type="submit" class="submit-btn">Continue</button>
            <a href="#" class="forget-password">Forget password?</a>

            <?php
                if(isset($_GET['error'])){
                    if($_GET['error'] == 'invalid_email'){
                        echo "<p class='error-message'>Invalid email format</p>";
                    } else if($_GET['error'] == 'password_too_short'){
                        echo "<p class='error-message'>Password must be at least 8 characters long</p>";
                    } else if($_GET['error'] == 'signin_failed'){
                        echo "<p class='error-message'>account not yet available</p>";
                    }
                }
            ?>
        </form>
    </div>
    <p class="or-label">doesn't have an account ?</p>
    <div class="swap-auth-container">
        <a href="signup.php" class="swap-auth-button">Create your Admin account</a>
    </div>
</body>
</html>