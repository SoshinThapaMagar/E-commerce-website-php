<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In as Trader</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
    
    ?>

    <div class="login-container">
    
        <div class="form-container">

            <a href="customerLogin.php" class="login-page-link">Customer Login</a>
            <a href="" class="login-page-link active-login-link">Trader Login</a>

            <form>

                <h2>Login to your Trader Account</h2>
                <input type="text" name="" id="" placeholder="Email Address">
                <input type="password" name="" id="" placeholder="Password">
                <input class="submit-btn" type="submit" value="Sign In">

                <a href="../register/traderRegister.php" class="register-link">Not a trader yet? <span>Request a trader account here</span></a>
            
            </form>
        
        </div>
    
    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>