<?php

    include '../init.php';

    if(isset($_POST['submit'])){
        $quantities=$_POST['quantities'];
        
        if(isset($_SESSION['userId'])){
            $userId = $_SESSION['userId'];
            $cartId=0;

            $cartIdQuery = "SELECT cart_id FROM HAMROMART.cart WHERE user_id=$userId";
            $cartIdQueryResult = oci_parse($connection, $cartIdQuery);
            oci_execute($cartIdQueryResult);
            if($cartIdQueryResult){
                while($cart = oci_fetch_assoc($cartIdQueryResult)){
                    $cartId = $cart['CART_ID'];
                }
            }


            foreach($quantities as $productNo=>$quantity){
                $updateQuery = "
                    UPDATE HAMROMART.cart_details cd
                    SET cd.product_quantity=$quantity[0]
                    WHERE cd.product_id=$productNo AND cd.cart_id=$cartId
                ";
    
                $updateQueryResult = oci_parse($connection, $updateQuery);
                oci_execute($updateQueryResult);

                if($updateQueryResult){
                    header('Location: cart.php');
                }
            }
        }
        else{
            // session_destroy($_SESSION['currentCart']);

            $_SESSION['currentCart']=array();
            foreach($quantities as $productNo=>$quantity){
                array_push($_SESSION['currentCart'],array($productNo=>$quantity[0]));
                // print_r($productNo);
                // print_r($quantity);
            }

            // print_r($_SESSION['currentCart']);
            header('Location: cart.php');
        }
        
    }

?>