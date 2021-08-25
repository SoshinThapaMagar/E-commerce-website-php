<?php

    include '../init.php';

    if(isset($_POST['submit-btn'])){

        $emailAddress = isset($_POST['email'])?$_POST['email']:'';
        $password = isset($_POST['password'])?$_POST['password']:'';

        if((!$emailAddress || empty($emailAddress)) || (!$password || empty($password))){
            $_SESSION['loginError']='Please fill in all the credentials.';
            header('Location: ./customerLogin.php');
        }
        else{

            $userExists = false;
            $userVerified = false;
            $userId=null;
            $userRole=null;

            // sanitizing
            $emailAddress=htmlspecialchars(trim($emailAddress));
            $password=md5(htmlspecialchars(trim($password)));
            
            $userQuery = "
                SELECT * FROM HAMROMART.users WHERE lower(user_email)=lower('$emailAddress') AND user_password='$password'
            ";
            $userQueryResult = oci_parse($connection, $userQuery);
            oci_execute($userQueryResult);

            if($userQueryResult){
                while($user = oci_fetch_assoc($userQueryResult)){
                    $userExists = true;
                    $userId=$user['USER_ID'];
                    $userRole = strtolower($user['USER_ROLE']);
                    
                    $userVerified = strtolower($user['VERIFIED'])=='true'?true:false;
                }

                if(!$userExists){
                    $_SESSION['loginError']='Invalid Email or Password.';
                    header('Location: ./customerLogin.php');
                }
                elseif(!$userVerified){
                    $_SESSION['loginError']='Your account has not been verified yet.';
                    header('Location: ./customerLogin.php');
                }
                else{
                    $_SESSION['userId']=$userId;
                    $_SESSION['userRole']=$userRole;

                    if($_SESSION['userRole']=='trader'){
                        header('Location: ../traderStats/traderStats.php');
                    }
                    elseif($_SESSION['userRole']=='admin'){
                        header('Location: ../admin/products.php');
                    }
                    else{


                        // MERGE CURRENT CART WITH USER'S CART FROM DATABASE
                        $customerCartItems = array();
                        $cartId=null;

                        // getting items from user's cart
                        $userCartQuery = "
                            SELECT cd.product_id, c.cart_id FROM HAMROMART.cart c
                            LEFT OUTER JOIN HAMROMART.cart_details cd ON c.cart_id=cd.cart_id
                            LEFT OUTER JOIN HAMROMART.users u ON u.user_id=c.user_id
                            WHERE u.user_id=$userId
                        ";
                        $userCartQueryResult = oci_parse($connection, $userCartQuery);
                        oci_execute($userCartQueryResult);

                        if($userCartQueryResult){
                            while($cartItem = oci_fetch_assoc($userCartQueryResult)){
                                array_push($customerCartItems, $cartItem['PRODUCT_ID']);
                                $cartId = $cartItem['CART_ID'];
                            }

                            print_r ($customerCartItems);
                        }

                        // getting current cart items
                        foreach($_SESSION['currentCart'] as $sessionCart ) {
                            foreach($sessionCart as $productId=>$productQuantity){
                                
                                if(!in_array($productId, $customerCartItems)){
                                    
                                    $productMergeQuery = "
                                        INSERT INTO HAMROMART.cart_details(cart_id, product_id, product_quantity)
                                        VALUES ($cartId, $productId, $productQuantity)
                                    ";
                                    echo "need to add this to cart";
                                    echo $productMergeQuery;
                                    $productMergeQueryResult = oci_parse($connection, $productMergeQuery);
                                    oci_execute($productMergeQueryResult);

                                }
                            }
                        }

                        header('Location: ../cart/cart.php');
                    }

                }
            }

        }

    }
    else{
        header('Location: ./customerLogin.php');
    }

?>