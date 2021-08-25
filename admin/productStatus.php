<?php

    include '../init.php';
    $productId = $_GET['product_id'];
    $status = $_GET['status'];

    if(!$productId || !$status){
        header('Location: ./products.php');
    }
    else{

        $productStatusQuery = "
            UPDATE HAMROMART.product SET disabled='$status' WHERE product_id=$productId
        ";
        $productStatusQueryResult = oci_parse($connection, $productStatusQuery);
        oci_execute($productStatusQueryResult);

        if($productStatusQueryResult){
            header("Location: ./productDetails.php?product_id=$productId");
        }
        else{
            header('Location: ./products.php');
        }

    }
?>