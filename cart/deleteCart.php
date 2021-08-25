<?php

    include '../init.php';

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

        $deleteQuery = "
            DELETE FROM HAMROMART.cart_details WHERE cart_id=$cartId
        ";

        $deleteQueryResult = oci_parse($connection, $deleteQuery);
        oci_execute($deleteQueryResult);

        if($deleteQueryResult){
            header('Location: cart.php');
        }
    }
    else{
        $_SESSION['currentCart']=array();
        header('Location: cart.php');
    }

?>