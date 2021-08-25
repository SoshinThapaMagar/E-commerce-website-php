<?php

    include '../init.php';
    
    $collectionTime= '';
    $collectionDay='';
    $slotId;
    $orderId;
    $cartId;
    $couponCode=isset($_GET['discount_coupon'])?$_GET['discount_coupon']:null;
    $couponId='NULL';

    if(!isset($_SESSION['userId'])){
        header('Location: ../login/customerLogin.php');
        return;
    }

    // no time date selected
    if(isset($_GET['collection_time']) && isset($_GET['collection_day'])){
        $collectionTime=$_GET['collection_time'];
        $collectionDay=$_GET['collection_day'];
        unset($_SESSION['orderError']);
    }
    else{
        $_SESSION['orderError']='You must select collection time and day.';
        header('Location: ./cart.php');
        return;
    }

    // cart items over 20
    if(sizeof($_SESSION['currentCart'])>20){
        $_SESSION['orderError']='You cannot order over 20 items at once.';
        header('Location: ./cart.php');
        return;
    }

    // get cart id
    $cartQuery = "
        SELECT cart_id FROM HAMROMART.cart c
        INNER JOIN HAMROMART.users u ON u.user_id = c.user_id
        WHERE u.user_id=$_SESSION[userId]
    ";
    $cartQueryResult=oci_parse($connection, $cartQuery);
    oci_execute($cartQueryResult);

    if($cartQueryResult){
        while($cart=oci_fetch_assoc($cartQueryResult)){
            $cartId = $cart['CART_ID'];
        }
    }

    // get coupon id
    $couponQuery = "
        SELECT coupon_id FROM HAMROMART.coupon WHERE coupon_code = '$couponCode'
    ";
    $couponQueryResult = oci_parse($connection, $couponQuery);
    oci_execute($couponQueryResult);

    if($couponQueryResult){
        while($coupon=oci_fetch_assoc($couponQueryResult)){
            $couponId = $coupon['COUPON_ID'];
        }
    }

    // generating collection sequence and inserting collection slot into that sequence
    $collectionSequenceQuery = "SELECT HAMROMART.slot_id_seq.NEXTVAL FROM dual";
    $collectionSequenceQueryResult = oci_parse($connection, $collectionSequenceQuery);
    oci_execute($collectionSequenceQueryResult);

    if($collectionSequenceQueryResult){
        while($collectionSequence = oci_fetch_assoc($collectionSequenceQueryResult)){
            $slotId = $collectionSequence['NEXTVAL'];
        }
    }
    $collectionQuery = "
        INSERT INTO HAMROMART.collection_slot(slot_id, collection_day, collection_time)
        VALUES($slotId,'$collectionDay', '$collectionTime')
    ";
    $collectionQueryResult = oci_parse($connection, $collectionQuery);
    oci_execute($collectionQueryResult);


    // generating order sequence and inserting order into that sequence with the previously generated collection sequence
    $orderSequenceQuery = "SELECT HAMROMART.order_id_seq.NEXTVAL FROM dual";
    $orderSequenceQueryResult = oci_parse($connection, $orderSequenceQuery);
    oci_execute($orderSequenceQueryResult);

    if($orderSequenceQueryResult){
        while($orderSequence = oci_fetch_assoc($orderSequenceQueryResult)){
            $orderId=$orderSequence['NEXTVAL'];
        }
    }

    $orderQuery = "INSERT INTO HAMROMART.orders(order_id, slot_id, cart_id, coupon_id) VALUES($orderId, $slotId, $cartId, $couponId)";
    $orderQueryResult = oci_parse($connection, $orderQuery);
    oci_execute($orderQueryResult);

    if($orderQueryResult){

        $cartDetailsQuery = "
            SELECT product_id, product_quantity
            FROM HAMROMART.cart_details cd
            INNER JOIN HAMROMART.cart c ON cd.cart_id = c.cart_id
            WHERE c.cart_id=$cartId
        ";

        $cartDetailsQueryResult = oci_parse($connection, $cartDetailsQuery);
        oci_execute($cartDetailsQueryResult);

        if($cartDetailsQueryResult){
            while($cartDetails=oci_fetch_assoc($cartDetailsQueryResult)){

                // get the product_id and product_quantity
                $productId = $cartDetails['PRODUCT_ID'];
                $productQuantity = $cartDetails['PRODUCT_QUANTITY'];

                // insert into order details
                $orderDetailsQuery = "
                    INSERT INTO HAMROMART.order_details(order_id, product_id, product_quantity)
                    VALUES($orderId, $productId, $productQuantity)
                ";
                $orderDetailsQueryResult = oci_parse($connection, $orderDetailsQuery);
                oci_execute($orderDetailsQueryResult);

                // update product stock
                $updateStockQuery = "
                    UPDATE HAMROMART.product 
                    SET stock=stock-$productQuantity
                    WHERE product_id=$productId
                ";
                $updateStockQueryResult = oci_parse($connection, $updateStockQuery);
                oci_execute($updateStockQueryResult);

            }

            // remove products from cart
            $deleteCartDetailsQuery = "
                DELETE from HAMROMART.cart_details where cart_id=$cartId
            ";
            $deleteCartQueryResult = oci_parse($connection, $deleteCartDetailsQuery);
            oci_execute($deleteCartQueryResult);

            if($deleteCartQueryResult){
                // send email
                $userEmail='';
                $userName='';
                $userEmailQuery = "SELECT user_name, user_email FROM HAMROMART.users WHERE user_id=$_SESSION[userId]";
                $userEmailQueryResult = oci_parse($connection, $userEmailQuery);
                oci_execute($userEmailQueryResult);

                if($userEmailQueryResult){
                    while($user = oci_fetch_assoc($userEmailQueryResult)){
                        $userEmail = $user['USER_EMAIL'];
                        $userName = $user['USER_NAME'];
                    }

                    $to = $userEmail;
                    $subject = "Order Confirmed";
                    $message = "Hi $userName, <br>
                    Your Order for $collectionTime, $collectionDay has been confirmed. <br>
                    Thank you for shopping with us.<br><br>
                    Regards: Hamro Mart";
                    $headers = "From: hloview@gmail.com \r\n";
                    $headers .= "MIME-Version: 1.0"."\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
                    if (mail($to, $subject, $message, $headers)) {
                        header('Location: ../orderDetails/orderDetails.php');
                    }
                    else{
                        echo "Order Failed";
                    }
                }

            }
   
        }

    }


    // $collectionQueryResult = mysqli_query($connection, $collectionQuery);
    // if($collectionQuery){

    //     $slotQuery = "SELECT * FROM collection_slot ORDER BY slot_id DESC LIMIT 1;";
    //     $slotQueryResult = mysqli_query($connection, $slotQuery);

    //     if($slotQueryResult){
            
    //         foreach($slotQueryResult as $slot){
    //             $slotId = $slot['slot_id'];
    //         }

    //         // insert into order after retrieving the slot id
    //         $orderQuery = "
    //             INSERT INTO orders(order_date, slot_id, cart_id, coupon_id)
    //             VALUES(NOW(), $slotId, $cartId, $couponId);
    //         ";

    //         $orderQueryResult = mysqli_query($connection, $orderQuery);
    //         if($orderQueryResult){

    //             $orderIdQuery = "SELECT order_id from orders ORDER BY order_id DESC LIMIT 1;";
    //             $orderIdQueryResult = mysqli_query($connection, $orderIdQuery);

    //             if($orderIdQueryResult){

    //                 foreach($orderIdQueryResult as $order){
    //                     $orderId = $order['order_id'];
    //                     echo $orderId;
    //                 }

    //                 // select all products from cart details and insert them into order details
    //                 $cartDetailsQuery = "
    //                     SELECT product_id, product_quantity
    //                     FROM cart_details cd
    //                     INNER JOIN cart c ON c.cart_id = c.cart_id
    //                     WHERE c.cart_id=$cartId;
    //                 ";

    //                 $cartDetailsQueryResult = mysqli_query($connection, $cartDetailsQuery);
    //                 if($cartDetailsQueryResult){
    //                     foreach($cartDetailsQueryResult as $cartDetails){

    //                         // get the product_id and product_quantity
    //                         $productId = $cartDetails['product_id'];
    //                         $productQuantity = $cartDetails['product_quantity'];

    //                         // insert into order details
    //                         $orderDetailsQuery = "
    //                             INSERT INTO order_details(order_id, product_id, product_quantity)
    //                             VALUES($orderId, $productId, $productQuantity);
    //                         ";
    //                         mysqli_query($connection, $orderDetailsQuery);

    //                         // update product stock
    //                         $updateStockQuery = "
    //                             UPDATE product 
    //                             SET stock=stock-$productQuantity
    //                             WHERE product_id=$productId;
    //                         ";
    //                         mysqli_query($connection, $updateStockQuery);

    //                     }

    //                     // delete from cart details
    //                     $deleteCartDetailsQuery = "
    //                         DELETE from cart_details where cart_id=$cartId;
    //                     ";
    //                     mysqli_query($connection, $deleteCartDetailsQuery);

    //                     // redirect to order
    //                     header('Location: ../orderDetails/orderDetails.php');
    //                 }

    //             }
                
    //         }

    //     }

    // }


?>