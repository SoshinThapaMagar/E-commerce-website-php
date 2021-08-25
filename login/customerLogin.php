<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In as Customer</title>
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

            <!-- <a href="" class="login-page-link active-login-link">Customer Login</a>
            <a href="traderLogin.php" class="login-page-link">Trader Login</a> -->

            <form method="POST" action="./login.php">
                <?php
                    if(isset($_SESSION['successful_update'])){
                        echo "<p style='color:#428035; font-weight:bold; margin-bottom:20px' class='successful-update'>$_SESSION[successful_update]</p>";
                        unset($_SESSION['successful_update']);
                    }
                ?> 
                <h2>Login to your account</h2>
                 
                <input type="text" name="email" id="" placeholder="Email Address">
                <input type="password" name="password" id="" placeholder="Password">

                <?php

                    if(isset($_SESSION['loginError'])){
                        echo "<p class='login-error'>$_SESSION[loginError]</p>";
                        unset($_SESSION['loginError']);
                    }
                    
                    if(isset($_SESSION['register_error'])){
                        echo "<p class='login-error'>$_SESSION[register_error]</p>";
                        unset($_SESSION['register_error']);
                    }
                
                ?>

                <input class="submit-btn" type="submit" value="Sign In" name="submit-btn">

                <a href="../register/customerRegister.php" class="register-link">Not a member yet? <span>Register here</span></a>
            
            </form>
        
        </div>
    
    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>