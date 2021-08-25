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

    <?php

        include '../init.php';

        // query to get all the cart products
        $productsQuery='';
        $productsQueryResult;


        $productsQueryResult=array();
        
        if(isset($_SESSION['currentCart'])){
            $cartSize = sizeof($_SESSION['currentCart']);
            if($cartSize==0){
                header('Location: ./noProducts.php');
            }
        }

        if(isset($_SESSION['userId'])){
            $productsQuery = "
            SELECT p.product_id, p.discount, p.product_name, p.product_image, p.product_price, p.stock, p.min_order, p.max_order, cd.product_quantity FROM HAMROMART.product p
            INNER JOIN HAMROMART.cart_details cd ON cd.product_id = p.product_id
            INNER JOIN HAMROMART.cart c ON c.cart_id=cd.cart_id
            INNER JOIN HAMROMART.users u ON u.user_id=c.user_id
            WHERE u.user_id=$_SESSION[userId]
            ";

            $queryResult = oci_parse($connection, $productsQuery);
            oci_execute($queryResult);

            while($currentProduct = oci_fetch_assoc($queryResult)){
                array_push($productsQueryResult, $currentProduct);
            }
        }
        else{

            if(isset($_SESSION['currentCart'])){

                foreach($_SESSION['currentCart'] as $product){

                    $currentKey = current(array_keys($product));
                    $currentValue = current(array_values($product));
    
                    $productsQuery="
                    SELECT p.product_id, p.product_name, p.discount, p.product_image, p.product_price, p.stock, p.min_order, p.max_order FROM HAMROMART.product p
                    WHERE p.product_id=$currentKey
                    ";
                    
                    $queryResult= oci_parse($connection, $productsQuery);
                    oci_execute($queryResult);

                    if($queryResult){
                        while($currentProduct=oci_fetch_assoc($queryResult)){
                            $currentProduct['PRODUCT_QUANTITY']=$product[$currentKey];
                            array_push($productsQueryResult,$currentProduct);
                        }
                    }
                }

            }

        }
    
    ?>

    <div class="cart-container">

        <div class="product-showcase">

            <form action="./updateCart.php" method="POST">
                <?php
                
                    foreach($productsQueryResult as $product){
                        
                        echo "<div class='products'>
                        <div class='product-image'>
                            <img src='".$product['PRODUCT_IMAGE']."' alt='".$product['PRODUCT_NAME']."'>
                        </div>
                        ";

                        echo "
                        <div class='product-description'>
                            <h2 class='product-name'>
                                ".$product['PRODUCT_NAME']."
                            </h2>

                            <label class='quantity-label'>Quantity: </label>
                        ";

                        $maxCartQuantity = $product['STOCK']<$product['MAX_ORDER']?$product['STOCK']:$product['MAX_ORDER'];
                        $minCartQuantity = $product['STOCK']<$product['MIN_ORDER']?$product['STOCK']:$product['MIN_ORDER'];

                        echo "<select name='quantities[$product[PRODUCT_ID]][]'>";
                        // for($i=$product['MIN_ORDER']; $i<$maxCartQuantity; $i++){
                        //     $selectedQuantity = $i==$product['PRODUCT_QUANTITY']?'selected':'';
                        //     echo "<option value='$i' $selectedQuantity>$i</option>";
                        // }
                        for($minCartQuantity; $minCartQuantity<=$maxCartQuantity; $minCartQuantity++){
                            $selectedQuantity = $minCartQuantity==$product['PRODUCT_QUANTITY']?'selected':'';
                            echo "<option value='$minCartQuantity' $selectedQuantity>$minCartQuantity</option>";
                        }
                        
                        echo "</select>";

                        echo "
                        <h3 class='product-price'>
                            &pound; ".($product['PRODUCT_PRICE']-($product['DISCOUNT']/100*$product['PRODUCT_PRICE']))."
                        </h3>";

                        echo "<a href='../cart/deleteCartProduct.php?productId=$product[PRODUCT_ID]'><svg class='trash-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z'/></svg></a>";
                        echo "</div></div>";
                        
                    }
            
                ?>

                <div class="cart-functionality-btn">
                    <input class="update-cart-btn" name="submit" type="submit" value="Update Cart">

                    <a class="delete-cart-btn" href="deleteCart.php">Delete Cart</a>
                </div>
            </form>
            
        </div>

        <div class="checkout-showcase">

            <div class="checkout-products">
            
                <?php
                    $checkoutSum=0;
                
                    foreach($productsQueryResult as $product){
                        
                        echo "<div class='products'>";
                        echo $product['PRODUCT_NAME'];

                        echo "<p class='product-quantity'>";
                        echo "X ".$product['PRODUCT_QUANTITY'];
                        echo "</p>";

                        echo "<p class='product-price'>";
                        echo (($product['PRODUCT_PRICE']-($product['DISCOUNT']/100*$product['PRODUCT_PRICE']))*$product['PRODUCT_QUANTITY']);
                        echo "</p>";

                        echo "</div>";

                        $checkoutSum+=($product['PRODUCT_PRICE']-($product['DISCOUNT']/100*$product['PRODUCT_PRICE']))*$product['PRODUCT_QUANTITY'];
                    }

                    echo "<h2 class='total-price'>";
                    echo "&pound; $checkoutSum";
                    echo "</h2>";
                
                ?>
            
            </div>
            <form method="POST" action="./verifyOrder.php?<?php echo 'checkout='.$checkoutSum?>">
                <input class="discount-input" type="text" placeholder="Discount Coupon" name="discount_coupon">

                <label for="">Select a collection day:</label>
                <select class="collection-day" name="collection_day">
                    <!-- <option value="" selected disabled>Select a Collection Day</option> -->
                </select>
                <label for="">Select a collection time:</label>
                <select class="collection-time" name="collection_time">
                    <!-- <option value="" selected disabled>Select a Collection Time</option> -->
                </select>
                
                <?php

                    if(isset($_SESSION['orderError'])){
                        echo "<h4 class='order-error'>$_SESSION[orderError]</h4>";
                        unset($_SESSION['orderError']);
                    }

                    if(isset($_SESSION['userId'])){
                        // echo "<div id='paypal-payment-button'>
                        // </div>";
                        echo "<input class='checkout-btn' type='submit' name='submit' value='Proceed to Checkout'>";
                    }
                    else{
                        echo "
                            <a class='checkout-btn' href='../login/customerLogin.php'>
                                Login Before Checkout
                            </a>
                        ";
                    }
                
                ?>

                
            </form>

        </div>

    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>
    
    <!-- <script src="https://www.paypal.com/sdk/js?client-id=AQWx7igehoamlx46L2d3sNCRVj8UpaJCHfebe-SwkMhSyK-QyAmLSHZYnd7DdwG_Nn6HDzBSe9ifzijS&disable-funding=credit,card"></script> -->
    <script src='./payment.js'></script>

    <script src="../navbar/navbar.js"></script>
    <script src="./collectionSlot.js"></script>

</body>
</html>