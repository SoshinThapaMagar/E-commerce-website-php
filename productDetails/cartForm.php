<?php

    include '../init.php';

    if(isset($_POST['submit'])){
        $productId=$_GET['productId'];
        $productQuantity=$_POST['productQuantity'];

        // user is logged in
        if(isset($_SESSION['userId'])){
            $userId=$_SESSION['userId'];

            // getting cart id of the current user
            $cartIdQuery = "SELECT cart_id from HAMROMART.cart c INNER JOIN HAMROMART.users u ON c.user_id=u.user_id WHERE u.user_id=$userId";
            $cartIds=oci_parse($connection, $cartIdQuery);
            oci_execute($cartIds);
            $userCartId=null;

            if($cartIds){
                while($currentId=oci_fetch_assoc($cartIds)){
                    $userCartId=$currentId['CART_ID'];
                }
            }

            // adding product to cart
            $addQuery = "INSERT INTO HAMROMART.cart_details VALUES($userCartId, $productId, $productQuantity)";
            $addQueryResult = oci_parse($connection, $addQuery);
            oci_execute($addQueryResult);
            if($addQueryResult){
                header('Location: ../cart/cart.php');
            }
        }
        // user isn't logged in
        else{
            if(isset($_SESSION['currentCart'])){
                array_push($_SESSION['currentCart'],array($productId=>$productQuantity));
            }
            else{
                $_SESSION['currentCart']=array();
                array_push($_SESSION['currentCart'],array($productId=>$productQuantity));
            }
            header('Location: ../cart/cart.php');
        }
        
    }

?>