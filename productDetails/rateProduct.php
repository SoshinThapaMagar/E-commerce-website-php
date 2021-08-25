<?php

    include '../init.php';


    if($_SESSION['userId']){
        $productId = $_GET['productId'];
        $rating = $_GET['ratingStar'];

        if(!$productId || !$rating){
            header('Location: ../login/customerLogin.php');
        }
        else{
            $ratingQuery = "SELECT * FROM HAMROMART.rating WHERE user_id=$_SESSION[userId] AND product_id=$productId";
            $ratingQueryResult = oci_parse($connection, $ratingQuery);
            oci_execute($ratingQueryResult);

            $rated=0;
            while($rating = oci_fetch_assoc($ratingQueryResult)){
                $rated++;
            }

            if($rated==0){
                
                $ratingQuery = "INSERT INTO HAMROMART.rating(user_id, rating_star, product_id) VALUES($_SESSION[userId], $_GET[ratingStar], $productId)";
                $ratingQueryResult = oci_parse($connection, $ratingQuery);
                echo $ratingQuery;
                oci_execute($ratingQueryResult);

                if($ratingQuery){
                    header("Location: ./productDetails.php?productId=$productId");
                }
            }
            else{
                // this line doesnot read $rating for some reason so i had to keep $_GET[ratingStar] in the query  = | weird

                $ratingQuery = "UPDATE HAMROMART.rating SET rating_star=$_GET[ratingStar] WHERE user_id=$_SESSION[userId] AND product_id=$productId";
                $ratingQueryResult = oci_parse($connection, $ratingQuery);
                oci_execute($ratingQueryResult);

                if($ratingQuery){
                    header("Location: ./productDetails.php?productId=$productId");
                }
            }
        }

    }
    else{
        header('Location: ../login/customerLogin.php');
    }

?>