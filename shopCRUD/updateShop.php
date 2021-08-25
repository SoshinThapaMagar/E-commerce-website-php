<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="updateProduct.css">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="addShop.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <title>Update Shop</title>
</head>
<body>

    <?php
        include '../navbar/navbar.php';
    ?>
    <?php
    include '../init.php';

    $shopExists = false;
    $_SESSION['url']="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $shop_id=$_GET['shop_id'];
    $sql="
        SELECT * FROM HAMROMART.shop s
        INNER JOIN HAMROMART.users u ON u.user_id=s.user_id
        WHERE shop_id=$shop_id AND u.user_id=$_SESSION[userId]
    ";

    $query=oci_parse($connection,$sql);
    oci_execute($query);

    if($query){
        while($row=oci_fetch_assoc($query)){
            $shopExists=true;
            $_SESSION['shop_name']=$row['SHOP_NAME'];
        }
    }

    if(!$shopExists){
        include '../401/401.php';
        exit();
    }
    
    ?>

    
    <div class="add-container">

        <div class="form-container">
        
            <h2 class="container-title">
                Update Shop
            </h2>

            <form action="<?php echo "update.php?shop_id=$shop_id";?>" method="POST">
                <input type="text" placeholder="Enter Shop Name" name="shop_name"
                value="<?php echo $_SESSION['shop_name'];?>">
                <?php
                    if(isset($_SESSION['error'])){
                        if($_SESSION['error']=="name"){
                            echo " shop name should not be empty.";
                            unset($_SESSION['error']);
                        }
                    }
                ?>

                <div class="form-actions">
                    <input class="delete-button" type="submit" value="Delete Shop" name="delete_submit" >
                    <input class="add-button" type="submit" value="Update Shop"name="submit">           
                </div>
            </form>
            <?php

            if(isset($_SESSION['status'])){
                if($_SESSION['status']=="success"){
                    echo "<p class='success-message'>Shop updated successfully.</p>";
                    unset($_SESSION['status']);
                }
                elseif ($_SESSION['status']=="fail") {
                    echo "<p class='error-message'>shop update fail.please try again.</p>";
                    unset($_SESSION['status']);
                }elseif($_SESSION['status']=="delete"){
                    unset($_SESSION['shop_name']);
                    echo "<p class='success-message'>shop deleted successfully.</p>";
                    unset($_SESSION['status']);
                }
               
            }
            ?>
        
        </div>

    </div>


    <?php
        include '../footer/footer.php';
    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>