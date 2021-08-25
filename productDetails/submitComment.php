<?php

    include '../init.php';

    if(isset($_SESSION['userId'])){
        $userId = $_SESSION['userId'];

        $productId = $_GET['productId'];
        $commentContent = $_POST['commentContent'];
    
        // sanitizing comment content
        $commentContent= htmlspecialchars($commentContent);
    
        $commentQuery = "
            INSERT INTO HAMROMART.comments(user_id, product_id, comment_content)
            VALUES($userId, $productId, '$commentContent')
        ";
        $commentQueryResult = oci_parse($connection, $commentQuery);
        oci_execute($commentQueryResult);
    
        if($commentQueryResult){
            header("Location: ./productDetails.php?productId=$productId");
        }
    }
    else{
        header('Location: ../login/customerLogin.php');
    }

?>