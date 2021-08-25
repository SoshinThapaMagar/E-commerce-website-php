<?php
    $connection = oci_connect('system', '12345', '//localhost/xe');
    error_reporting(0);
    session_start();

    if(isset($_SESSION['userId'])&&(isset($_SESSION['userRole'])&&$_SESSION['userRole']=='customer')){
        $_SESSION['currentCart']=array();
        $cartQuery = "
            SELECT p.product_id, cd.product_quantity FROM HAMROMART.product p
            INNER JOIN HAMROMART.cart_details cd ON cd.product_id = p.product_id
            INNER JOIN HAMROMART.cart c ON c.cart_id=cd.cart_id
            INNER JOIN HAMROMART.users u ON u.user_id=c.user_id
            WHERE u.user_id=$_SESSION[userId]
        ";

        $cartQueryResult = oci_parse($connection, $cartQuery);
        oci_execute($cartQueryResult);

        if($cartQueryResult){
            while($cart=oci_fetch_assoc($cartQueryResult)){
                if(isset($_SESSION['currentCart'])){
                    array_push($_SESSION['currentCart'],array($cart['PRODUCT_ID']=>$cart['PRODUCT_QUANTITY']));
                }
                else{
                    $_SESSION['currentCart']=array();
                    array_push($_SESSION['currentCart'],array($cart['PRODUCT_ID']=>$cart['PRODUCT_QUANTITY']));
                }
            }

        }
    }

    if(!isset($_SESSION['currentCart'])){
        $_SESSION['currentCart']=array();
    }
?>