<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traders</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="details.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        $productId = $_GET['product_id'];
        if(!$productId){
            header('Location: ./products.php');
            exit();
        }

        if(!isset($_SESSION['userId'])||(isset($_SESSION['userRole']) && $_SESSION['userRole']!='admin')){
            include '../401/401.php';
            exit();
        }
    
    ?>

    <div class="details-container">

        <h2 class="container-title">
            Product Details...
        </h2>

        <div class="product">
        
            <?php
            
                $productQuery = "
                    SELECT
                        AVG(r.rating_star) rating,
                        u.user_name,
                        s.shop_name,
                        p.product_price,
                        p.product_name,
                        p.stock,
                        p.product_image, 
                        p.product_description,
                        p.allergy_information,
                        p.disabled
                    FROM HAMROMART.product p
                    INNER JOIN HAMROMART.shop s ON s.shop_id=p.shop_id
                    INNER JOIN HAMROMART.users u ON u.user_id=s.shop_id
                    INNER JOIN HAMROMART.rating r ON r.product_id=p.product_id
                    WHERE p.product_id=$productId
                    GROUP BY u.user_name, s.shop_name, p.product_price, p.product_name, p.stock, p.product_image, p.product_description, p.allergy_information, p.disabled
                ";
                $productQueryResult = ociparse($connection, $productQuery);
                oci_execute($productQueryResult);

                if($productQueryResult){
                    while($product = oci_fetch_assoc($productQueryResult)){
                        
                        echo "<div class='image-container'>";
                        echo "<img src='$product[PRODUCT_IMAGE]'>";
                        echo "</div>";
                        $rating = round($product['RATING'],2);

                        // 
                        echo "<div class='product-details'>";
                        echo "<p><label>Product Name:</label> $product[PRODUCT_NAME]</p>";
                        echo "<p><label>Trader Name:</label> $product[USER_NAME]</p>";
                        echo "<p><label>Shop Name: </label>$product[SHOP_NAME]</p>";
                        echo "<p><label>Product Price:</label> $product[PRODUCT_PRICE]</p>";
                        echo "<p><label>Product Stock:</label> $product[STOCK]</p>";
                        echo "<p><label>Description:</label> $product[PRODUCT_DESCRIPTION]</p>";
                        echo "<p><label>Allergy Information:</label> $product[ALLERGY_INFORMATION]</p>";
                        echo "<p><label>Average Rating:</label> $rating</p>";

                        // disable button
                        if(strtolower($product['DISABLED'])=='false'){
                            echo "<a href='./productStatus.php?product_id=$productId&status=TRUE' class='disable-product'>Disable Product</a>";
                        }
                        else{
                            echo "<a href='./productStatus.php?product_id=$productId&status=FALSE' class='disabled'>Product Disabled</a>";
                        }

                        echo "</div>";

                    }
                }
                else{
                    header('Location: ./products.php');
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