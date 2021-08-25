<?php

    include '../init.php';
    $productId = $_GET['productId'];

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
            DELETE FROM HAMROMART.cart_details WHERE product_id=$productId AND cart_id=$cartId
        ";
        $deleteQueryResult = oci_parse($connection, $deleteQuery);
        oci_execute($deleteQueryResult);
    
        if($deleteQueryResult){
            header('Location: cart.php');
        }
    }
    else{
        // print_r($_SESSION['currentCart']);
        for($i=0;$i<sizeof($_SESSION['currentCart']);$i++){
            foreach($_SESSION['currentCart'][$i] as $cartProductId=>$cartProductQuantity){
                if($cartProductId==$productId){
                    // unset($_SESSION['currentCart'][$i]);
                    array_splice($_SESSION['currentCart'],$i,1);
                    header('Location: cart.php');
                }
            }
        }

        // foreach($_SESSION['currentCart'] )
    }

?>