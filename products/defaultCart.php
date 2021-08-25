<?php

    include '../init.php';

    $productId = $_GET['productId'];
    $minOrder = 1;

    $minOrderQuery = "
        SELECT min_order FROM HAMROMART.product WHERE product_id=$productId
    ";
    $minOrderQueryResult = oci_parse($connection, $minOrderQuery);
    oci_execute($minOrderQueryResult);

    if($minOrderQueryResult){
        while($product = oci_fetch_assoc($minOrderQueryResult) ){
            $minOrder=$product['MIN_ORDER'];

            // check if user is logged in or not
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
                $addQuery = "INSERT INTO HAMROMART.cart_details VALUES($userCartId, $productId, $minOrder)";
                $addQueryResult = oci_parse($connection, $addQuery);
                oci_execute($addQueryResult);
                if($addQueryResult){
                    header('Location: ../cart/cart.php');
                }
            }
            else{
                if(isset($_SESSION['currentCart'])){
                    array_push($_SESSION['currentCart'],array($productId=>$minOrder));
                }
                else{
                    $_SESSION['currentCart']=array();
                    array_push($_SESSION['currentCart'],array($productId=>$minOrder));
                }
                header('Location: ../cart/cart.php');
            }
        }
    }

?>