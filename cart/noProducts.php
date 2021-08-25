<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="cart.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';

        if((isset($_SESSION['userRole']) && $_SESSION['userRole']!='customer')){
            include '../401/401.php';
            exit();
        }
    
    ?>

    <div class="empty-container">
        <h3>You do not have any products in your cart.</h3>
    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>
    

    <script src="../navbar/navbar.js"></script>
    <script src="./collectionSlot.js"></script>

</body>
</html>