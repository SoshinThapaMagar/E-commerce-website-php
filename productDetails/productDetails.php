<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="productDetails.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <title>Product Details</title>
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        if((isset($_SESSION['userRole']) && $_SESSION['userRole']!='customer')){
            include '../401/401.php';
            exit();
        }
    
    ?>

    <?php
        include '../init.php';
        $productCategoryId=null;

        $productId = $_GET['productId'];
        $productQuery = "
            SELECT p.*, t.category_id, u.user_name FROM HAMROMART.product p 
            INNER JOIN HAMROMART.shop s ON s.shop_id=p.shop_id 
            INNER JOIN HAMROMART.users u ON u.user_id = s.user_id
            INNER JOIN HAMROMART.trader_category t ON t.category_id = u.category_id
            WHERE product_id=$productId
        ";
        $products = oci_parse($connection, $productQuery);
        oci_execute($products);

        $currentProduct=null;
        
        while($product=oci_fetch_assoc($products)){
            $currentProduct=$product;
            $productCategoryId=$product['CATEGORY_ID'];
        }
    ?>

    <div class="main-container">

        <div class="details-container">

            <div class="image-container">
                <img src="<?php echo($currentProduct['PRODUCT_IMAGE']) ?>" alt="">
            </div>

            <div class="text-container">

                <h2 class="product-name">
                    <?php
                        echo $currentProduct['PRODUCT_NAME'];
                    ?>
                </h2>
                <p class="trader-name">
                    <?php
                        echo $currentProduct['USER_NAME'];
                    ?>
                </p>

                <div class="product-rating">

                   <?php

                        // getting all the ratings for the product
                        $productRated = false;
                        $averageRating=0;
                        $numberOfUsers=0;

                        $ratingQuery = "SELECT * FROM HAMROMART.rating WHERE product_id=$productId";
                        $ratingQueryResult = oci_parse($connection, $ratingQuery);
                        oci_execute($ratingQueryResult);

                        if($ratingQueryResult){

                            while($rating=oci_fetch_assoc($ratingQueryResult)){
                                if(isset($_SESSION['userId']) && $rating['USER_ID']==$_SESSION['userId']){
                                    $productRated=$rating['RATING_STAR'];
                                }
                                $numberOfUsers++;

                                // adding to rating
                                $averageRating+=(int)$rating['RATING_STAR'];
                            }

                            if($numberOfUsers>0){
                                $averageRating=$averageRating/$numberOfUsers;                                
                            }
                        }

                        for($i=1; $i<=5; $i++){
                            if($i<=$averageRating){
                                echo "
                                    <svg class='filled-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'/></svg>
                                ";
                            }else{
                                echo "
                                    <svg class='stroke-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 5.173l2.335 4.817 5.305.732-3.861 3.71.942 5.27-4.721-2.524-4.721 2.525.942-5.27-3.861-3.71 5.305-.733 2.335-4.817zm0-4.586l-3.668 7.568-8.332 1.151 6.064 5.828-1.48 8.279 7.416-3.967 7.416 3.966-1.48-8.279 6.064-5.827-8.332-1.15-3.668-7.569z'/></svg>
                                ";
                            }

                        }
                        echo "<span>($numberOfUsers)</span>";

                   
                   ?>

                </div>

                <div class="product-price">
                    <?php
                    
                        if($currentProduct['DISCOUNT']>0){
                            echo "
                            <span class='discount'>
                                &pound;
                                $currentProduct[PRODUCT_PRICE]
                            </span>
                            ";
                        }
                    
                    ?>

                    <h2 class="price">
                        <?php
                            echo "&pound ".($currentProduct['PRODUCT_PRICE']-($currentProduct['DISCOUNT']/100*$currentProduct['PRODUCT_PRICE']));
                        ?>
                    </h2>
                </div>

                <!-- show product out of stock -->
                <?php
                    $productInCart=false;
                    foreach($_SESSION['currentCart'] as $currCart){
                        foreach($currCart as $cartProductId=>$cartProductQuantity){
                            if($cartProductId==$productId){
                                $productInCart=true;
                            }
                        }
                    }
                
                    if($currentProduct['STOCK']<1){
                        echo "<h3 class='out-of-stock'>Product out of stock</h3>";
                    }
                    elseif($productInCart){
                        echo "<a class='already-in-cart' href='../cart/cart.php'>Product already in cart</a>";
                    }
                    elseif(sizeof($_SESSION['currentCart'])>=20){
                        echo "<h3 class='out-of-stock'>Your cart is already at a limit of 20 items.</h3>";
                    }
                    elseif($currentProduct['DISABLED']=='TRUE'){
                        echo "<h3 class='out-of-stock'>This product has been disabled by the admin.</h3>";
                    }
                    else{
                        echo "<div class='cart-functionalities'>";
                        echo "<form action='./cartForm.php?productId=$productId' method='POST'>";
                        echo "<select name='productQuantity'>";
                        $maxCartQuantity = $currentProduct['STOCK']<$currentProduct['MAX_ORDER']?$currentProduct['STOCK']:$currentProduct['MAX_ORDER'];
                        $minCartQuantity = $currentProduct['STOCK']<$currentProduct['MIN_ORDER']?$currentProduct['STOCK']:$currentProduct['MIN_ORDER'];

                        for($minCartQuantity; $minCartQuantity<=$maxCartQuantity; $minCartQuantity++){
                            // $selectedQuantity = ($minCartQuantity==$currentProduct['MIN_ORDER'])?'selected':'';
                            echo "<option value='$minCartQuantity' $selectedQuantity>$minCartQuantity</option>";
                        }
                        echo "</select>";
                        echo "<input type='submit' name='submit' value='Add to Cart'>";
                        echo "</form>";
                        echo "</div>";
                    }
                
                ?>

                <div class="description-container">

                    <p class="desc-container-title">
                        description
                    </p>

                    <p class="product-description">
                        <?php
                            echo $currentProduct['PRODUCT_DESCRIPTION'];
                        ?>
                    </p>

                    <div class="allergy-info-container">

                        <svg class="allergy-arrow" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"/></svg>

                        <p class="allergy-title">
                            Allergy Information
                        </p>

                        <p class="allergy-content">
                            <?php
                                echo $currentProduct['ALLERGY_INFORMATION'];
                            ?>                        
                        </p>
                    </div>
                    
                    <?php

                        // if user has already rated the product, update the rating else insert a new rating.
                        if(!$productRated){

                            echo "<div class='rate-product'><span>Rate this product: </span>";
                            for($i=1;$i<=5;$i++){
                                $productId=(int)$productId;
                                echo "
                                    <a href='./rateProduct.php?productId=$productId&ratingStar=$i'>
                                        <svg class='stroke-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 5.173l2.335 4.817 5.305.732-3.861 3.71.942 5.27-4.721-2.524-4.721 2.525.942-5.27-3.861-3.71 5.305-.733 2.335-4.817zm0-4.586l-3.668 7.568-8.332 1.151 6.064 5.828-1.48 8.279 7.416-3.967 7.416 3.966-1.48-8.279 6.064-5.827-8.332-1.15-3.668-7.569z'/></svg>
                                    </a>
                                ";

                            }

                            echo "</div>";
                        }
                        else{
                            echo "<div class='rate-product'><span>You rated this product: </span>";
                            for($i=1;$i<=5;$i++){
                                $productId=(int)$productId;
                                if($i<=$productRated){
                                    echo "
                                    <a href='./rateProduct.php?productId=$productId&ratingStar=$i'>
                                        <svg class='filled-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'/></svg>
                                    </a>
                                ";
                                }
                                else{
                                    echo "
                                    <a href='./rateProduct.php?productId=$productId&ratingStar=$i'>
                                        <svg class='stroke-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 5.173l2.335 4.817 5.305.732-3.861 3.71.942 5.27-4.721-2.524-4.721 2.525.942-5.27-3.861-3.71 5.305-.733 2.335-4.817zm0-4.586l-3.668 7.568-8.332 1.151 6.064 5.828-1.48 8.279 7.416-3.967 7.416 3.966-1.48-8.279 6.064-5.827-8.332-1.15-3.668-7.569z'/></svg>
                                    </a>
                                    ";
                                }

                            }

                            echo "</div>";
                        }

                    ?>

                </div>

            </div>

        </div>

        <hr>

        <div class="comments-container">
            <h2 class="comments-container-title">
                What Others Think of this <br> Product...
            </h2>

            <?php

                // retrieving all comments from database
                $commentQuery = "
                    SELECT c.comment_content, u.user_name
                    FROM HAMROMART.comments c
                    INNER JOIN HAMROMART.users u ON u.user_id=c.user_id
                    INNER JOIN HAMROMART.product p ON p.product_id=c.product_id
                    WHERE p.product_id=$productId
                ";

                $commentQueryResult = oci_parse($connection, $commentQuery);
                oci_execute($commentQueryResult);
            
            ?>

            <div class="comment-section">

                <div class="comment-box">
                    <form method="POST">
                        <input type="text" name="commentContent" placeholder="Leave a review...">

                        <button type="submit" formaction="<?php echo"submitComment.php?productId=$productId"?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M1.439 16.873l-1.439 7.127 7.128-1.437 16.873-16.872-5.69-5.69-16.872 16.872zm4.702 3.848l-3.582.724.721-3.584 2.861 2.86zm15.031-15.032l-13.617 13.618-2.86-2.861 10.825-10.826 2.846 2.846 1.414-1.414-2.846-2.846 1.377-1.377 2.861 2.86z"/></svg>
                        </button>
                    </form>
                </div>

                <div class="comments">

                    <?php

                        $noOfComments=0;
                        while($comment=oci_fetch_assoc($commentQueryResult)){
                            echo "<div class='comment'>";
                            echo "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm7.753 18.305c-.261-.586-.789-.991-1.871-1.241-2.293-.529-4.428-.993-3.393-2.945 3.145-5.942.833-9.119-2.489-9.119-3.388 0-5.644 3.299-2.489 9.119 1.066 1.964-1.148 2.427-3.393 2.945-1.084.25-1.608.658-1.867 1.246-1.405-1.723-2.251-3.919-2.251-6.31 0-5.514 4.486-10 10-10s10 4.486 10 10c0 2.389-.845 4.583-2.247 6.305z'/></svg>";

                            echo "
                                <div class='comment-info'>
                                    <p class='user-name'>$comment[USER_NAME]</p>
        
                                    <p class='comment-content'>
                                        $comment[COMMENT_CONTENT]
                                    </p>
                                </div>
                            ";

                            echo "</div>";
                            $noOfComments++;
                        }

                        if($noOfComments==0){
                            echo "<h3 class='no-comment'>This product does not have any comments</h3>";
                        }

                    
                    ?>

                </div>

            </div>
        </div>

        <hr>

        <div class="recommendation-container">

            <h2 class="recommendation-container-title">
                Similar Products You <br>
                Might Like...
            </h2>

            <div class="products">

                <?php
                    $similarProductsCount=0;
                    $recommendationQuery = "
                        SELECT p.product_id, p.product_name, p.product_image, p.product_price
                        FROM HAMROMART.product p
                        INNER JOIN HAMROMART.shop s ON p.shop_id=s.shop_id
                        INNER JOIN HAMROMART.users u ON u.user_id=s.user_id
                        INNER JOIN HAMROMART.trader_category t ON t.category_id=u.category_id
                        WHERE t.category_id=$productCategoryId AND p.product_id<>$productId AND p.stock>0 AND upper(p.disabled)='FALSE'
                    ";

                    $recommendationQueryResult = oci_parse($connection, $recommendationQuery);
                    oci_execute($recommendationQueryResult);

                    if($recommendationQueryResult){
                        while($product=oci_fetch_assoc($recommendationQueryResult)){
                            $similarProductsCount++;

                            echo "<div class='product'>";

                            echo "<div class='product-image'>";
                            echo "<img loading='lazy' src='$product[PRODUCT_IMAGE]' alt='$product[PRODUCT_NAME]'>";
                            echo "</div>";

                            echo "
                                <a style='color:black; text-decoration:none;' href='../productDetails/productDetails.php?productId=$product[PRODUCT_ID]'>
                                    <h3 class='product-name'>
                                        $product[PRODUCT_NAME]
                                    </h3>
                                </a>
                            ";

                            echo "<div class='product-rating'>";

                            $productRating=0;
                            $ratedUsers=0;

                            $ratingQuery = "SELECT * FROM HAMROMART.rating WHERE product_id=$product[PRODUCT_ID]";
                            $ratingQueryResult = oci_parse($connection, $ratingQuery);
                            oci_execute($ratingQueryResult);

                            if($ratingQueryResult){

                                while($rating=oci_fetch_assoc($ratingQueryResult)){

                                    // adding to rating
                                    $productRating+=(int)$rating['RATING_STAR'];
                                    $ratedUsers++;
                                }

                                if($ratedUsers>0){
                                    $productRating=$productRating/$ratedUsers;                                
                                }
                            }

                            for($i=1; $i<=5; $i++){
                                if($i<=$productRating){
                                    echo "
                                        <svg class='filled-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z'/></svg>
                                    ";
                                }else{
                                    echo "
                                        <svg class='stroke-star' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 5.173l2.335 4.817 5.305.732-3.861 3.71.942 5.27-4.721-2.524-4.721 2.525.942-5.27-3.861-3.71 5.305-.733 2.335-4.817zm0-4.586l-3.668 7.568-8.332 1.151 6.064 5.828-1.48 8.279 7.416-3.967 7.416 3.966-1.48-8.279 6.064-5.827-8.332-1.15-3.668-7.569z'/></svg>
                                    ";
                                }

                            }

                            echo "</div>";

                            if(!$productInCart && sizeof($_SESSION['currentCart'])<20){
                                echo "
                                <a href='../products/defaultCart.php?productId=$product[PRODUCT_ID]'>
                                    <svg class='cart-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M24 3l-.743 2h-1.929l-3.474 12h-13.239l-4.615-11h16.812l-.564 2h-13.24l2.937 7h10.428l3.432-12h4.195zm-15.5 15c-.828 0-1.5.672-1.5 1.5 0 .829.672 1.5 1.5 1.5s1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5zm6.9-7-1.9 7c-.828 0-1.5.671-1.5 1.5s.672 1.5 1.5 1.5 1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5z'/></svg>
                                </a>
                                ";
                            }

                            echo "
                                <h3 class='product-price'>
                                    &pound; $product[PRODUCT_PRICE]
                                </h3>
                            ";

                            echo "</div>";

                        }

                        if($similarProductsCount==0){
                            echo "<h3>No Other Products Similar to This Found =(</h3>";
                        }
                    }
                
                ?>
                
            </div>

        </div>

    </div>

    <?php
    
        include '../footer/footer.php';

    ?>

    

    <script src="../navbar/navbar.js"></script>
    <script src="./script.js"></script>
    
</body>
</html>