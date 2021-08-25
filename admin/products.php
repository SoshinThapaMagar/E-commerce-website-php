<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traders</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="products.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        if(!isset($_SESSION['userId'])||(isset($_SESSION['userRole']) && $_SESSION['userRole']!='admin')){
            include '../401/401.php';
            exit();
        }
    
    ?>

    <div class="products-container">

        <div class="product-headers">
            <h2 class="container-title">All Products...</h2>
            <form method="GET">
                <input type="text" name="product_search" placeholder="Search Product...">
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z"/></svg>
                </button>
            </form>
        </div>

        <div class="products">
            <?php
                $noOfProducts = 0;
                $productName = isset($_GET['product_search'])?$_GET['product_search']:'';
            
                $productsQuery = "
                    SELECT p.product_id, p.disabled, p.product_name, u.user_id, u.user_name, p.product_image FROM HAMROMART.product p 
                    INNER JOIN HAMROMART.shop s ON s.shop_id=p.shop_id
                    INNER JOIN HAMROMART.users u ON u.user_id = s.user_id
                    WHERE lower(p.product_name) LIKE lower('%$productName%')
                ";
                $productsQueryResult = oci_parse($connection, $productsQuery);
                oci_execute($productsQueryResult);

                if($productsQueryResult){
                    while($product = oci_fetch_assoc($productsQueryResult)){
                        $noOfProducts++;
                        echo "<div class='product'>";

                        echo "<span class='product-id'>$product[PRODUCT_ID]</span>";
                        echo "
                            <div class='product-info'>
                                <div class='product-image'>
                                    <img src='$product[PRODUCT_IMAGE]' alt='$product[PRODUCT_NAME]'>
                                </div>

                                <div>
                                    <p class='product-name'>$product[PRODUCT_NAME]</p>
                                    <p class='trader-name'>$product[USER_NAME]</p>
                                </div>   
                            </div>
                        ";

                        if(strtolower($product['DISABLED'])=='true'){
                            echo "
                            <a class='disabled' href='./productDetails.php?product_id=$product[PRODUCT_ID]'>
                                See Details
                            </a>";
                        }
                        else{
                            echo "
                            <a href='./productDetails.php?product_id=$product[PRODUCT_ID]'>
                                See Details
                            </a>";
                        }

                        echo "</div>";
                    }

                    if($noOfProducts==0){
                        echo "<h3>No Products Found.</h3>";
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