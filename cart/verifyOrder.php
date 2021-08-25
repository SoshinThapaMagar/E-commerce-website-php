<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Order</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="cart.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        if(isset($_POST['submit'])){

            $collectionTime = $_POST['collection_time'];
            $collectionDay = $_POST['collection_day'];
            $checkoutSum = $_GET['checkout'];
            $couponCode = $_POST['discount_coupon'];
            $cartNo=0;
            $discountPercent= 0;

            // check for collection slot order limit
            $collectionLimitQuery = "
                SELECT COUNT(*) AS NO_OF_ORDERS FROM HAMROMART.orders o 
                INNER JOIN HAMROMART.collection_slot cs ON cs.slot_id=o.slot_id
                WHERE CURRENT_DATE - TO_DATE(o.order_date)<=7
                AND (cs.collection_day='$collectionDay' AND cs.collection_time='$collectionTime')
            ";

            $collectionLimitQueryResult = oci_parse($connection, $collectionLimitQuery);
            oci_execute($collectionLimitQueryResult);
            $noOfOrders=0;

            if($collectionLimitQueryResult){
                while($collection = oci_fetch_assoc($collectionLimitQueryResult)){
                    $noOfOrders=$collection['NO_OF_ORDERS'];
                }

                if($noOfOrders>=20){
                    // give warning
                    $_SESSION['orderError']='The collection slot is already full, please select another slot';
                    header('Location: ./cart.php');
                    exit();
                }
            }
            
        }
        else{
            header('Location: ./cart.php');
            exit();
        }
    
    ?>

    <div class="confirm-checkout">

        <h2 class="container-title">
            Your Checkout <br> Details...
        </h2>

        <div class="products">
            <?php

                echo "<p class='order-collection-details'>Collection Time: <span id='collection-time'>$collectionTime</span></p>";
                echo "<p class='order-collection-details'>Collection Day: <span id='collection-day'>$collectionDay</span></p><hr>";
            
                $cartQuery = "SELECT cart_id FROM HAMROMART.cart WHERE user_id=$_SESSION[userId]";
                $cartQueryResult = oci_parse($connection, $cartQuery);
                oci_execute($cartQueryResult);

                if($cartQueryResult){
                    while($cart = oci_fetch_assoc($cartQueryResult)){
                        $cartNo = $cart['CART_ID'];
                    }
                }

                $cartDetailsQuery = "
                    SELECT p.product_name, cd.product_quantity FROM HAMROMART.cart_details cd 
                    INNER JOIN HAMROMART.product p ON cd.product_id=p.product_id
                    WHERE cart_id=$cartNo
                    ";
                $cartDetailsQueryResult = oci_parse($connection, $cartDetailsQuery);
                oci_execute($cartDetailsQueryResult);

                if($cartDetailsQueryResult){
                    while($cart = oci_fetch_assoc($cartDetailsQueryResult)){
                        echo "
                            <div class='product'>
                                <span class='product-name'>
                                    $cart[PRODUCT_NAME]
                                </span>
                                </span class='product-quantity'>
                                    X $cart[PRODUCT_QUANTITY]
                                </span>
                            </div>
                        ";
                    }
                }

                $couponQuery = "SELECT discount_percent FROM HAMROMART.coupon WHERE coupon_code='$couponCode'";
                $couponQueryResult = oci_parse($connection, $couponQuery);
                oci_execute($couponQueryResult);
                
                if($couponQueryResult){
                    while($coupon = oci_fetch_assoc($couponQueryResult)){
                        $discountPercent = $coupon['DISCOUNT_PERCENT'];
                        $checkoutSum = $checkoutSum - (($discountPercent/100)*$checkoutSum);

                    }
                }
                echo "<hr>";
                echo "<h4 class='discount-percent'>Coupon Discount: <span id='discount-coupon'>$couponCode</span></h4>";
                echo "<h4 class='discount-percent'>Coupon Discount: $discountPercent</h4>";
                echo "<h3 class='product-checkout-sum'>Checkout Price: &pound; $checkoutSum</h3>";

                echo "<div class='checkout-options'>";
                echo "<a href='./cart.php' class='cancel-btn'>Cancel Order</a>";

                // checkout options
                echo "<div id='paypal-payment-button'>
                </div>";
                echo "</div>";
        
            ?>
        </div>
        
    </div>


    <?php
    
        include '../footer/footer.php';
    
    ?>
    
    <script src="https://www.paypal.com/sdk/js?client-id=AQWx7igehoamlx46L2d3sNCRVj8UpaJCHfebe-SwkMhSyK-QyAmLSHZYnd7DdwG_Nn6HDzBSe9ifzijS&disable-funding=credit,card"></script>
    <script src='./payment.js'></script>

    <script src="../navbar/navbar.js"></script>

</body>
</html>