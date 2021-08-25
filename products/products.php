<?php
    include "../init.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="products.css">  
    <link rel="stylesheet" href="../navbar/navbar.css">
    <title>All Products</title>
</head>
<body>

    <?php

        include "../navbar/navbar.php";
        if((isset($_SESSION['userRole']) && $_SESSION['userRole']!='customer')){
            include '../401/401.php';
            exit();
        }
        
    ?>

    <div class="products-container">


        <div class="products-showcase">
        
        
            <?php 
                // form values

                // product name
                $productName = (isset($_GET['product_name']) && !empty($_GET['product_name']))?strtolower($_GET['product_name']):'';
                $_SESSION['searchData']=$productName;

                // trader category
                $traderCategory = (isset($_GET['product-type']) && !empty($_GET['product-type']))?$_GET['product-type']:'';
                $_SESSION['traderCategory']=$traderCategory;

                // shop name
                $shopName = (isset($_GET['shop']) && !empty($_GET['shop']))?$_GET['shop']:'';
                $_SESSION['shopName']=$shopName;

                //price range
                $minPrice = 0;
                if(isset($_GET['min_price']) && !empty($_GET['min_price'])){
                    $minPrice=$_GET['min_price'];
                    $_SESSION['minData']=$_GET['min_price'];
                }

                $maxPrice = 999999;
                if(isset($_GET['max_price']) && !empty($_GET['max_price'])){
                    $maxPrice=$_GET['max_price'];
                    $_SESSION['maxData']=$_GET['max_price'];
                }

                //rating
                $ratingLimit = (isset($_GET['rating']) && !empty($_GET['rating']))?$_GET['rating']:0;
                $_SESSION['rateData']=$ratingLimit;

                // building the search query
                $searchQuery = "
                    SELECT p.product_id, p.product_name, p.product_price, p.discount, p.product_image, u.user_name
                    FROM HAMROMART.product p
                    INNER JOIN HAMROMART.shop s ON s.shop_id = p.shop_id
                    INNER JOIN HAMROMART.users u ON u.user_id = s.user_id
                    INNER JOIN HAMROMART.trader_category t ON t.category_id=u.category_id
                    WHERE p.disabled <> 'TRUE' AND p.stock>0 
                    AND lower(p.product_name) LIKE lower('%$productName%') 
                    AND t.category_type LIKE '%$traderCategory%'
                    AND s.shop_name LIKE '%$shopName%'
                    AND p.product_price BETWEEN $minPrice AND $maxPrice
                ";
                $searchQueryResult = oci_parse($connection, $searchQuery);
                oci_execute($searchQueryResult);

                if($searchQueryResult){

                    // no products found
                    // if(oci_fetch_all($searchQueryResult, $res)<=0){
                    //     echo "<h3>No Products Found =(</h3>";
                    // }

                    $productsDisplayed=0;

                    // display product
                    while($product = oci_fetch_assoc($searchQueryResult) ){
                        // checking if product is already in cart
                        // if it is in cart, the cart in the thumbnail will be not be shown
                        $productInCart = false;
                        foreach($_SESSION['currentCart'] as $currCart){
                            foreach($currCart as $cartProductId=>$cartProductQuantity){
                                if($cartProductId==$product['PRODUCT_ID']){
                                    $productInCart=true;
                                }
                            }
                        }

                        // getting product ratng

                        $ratingQuery = "SELECT rating_star FROM HAMROMART.rating WHERE product_id=$product[PRODUCT_ID]";
                        $ratingQueryResult = oci_parse($connection, $ratingQuery);
                        oci_execute($ratingQueryResult);

                        if($ratingQueryResult){
                            $noOfUsers = 0;
                            $averageRating=0;

                            while($rating = oci_fetch_assoc($ratingQueryResult)){
                                $averageRating+=$rating['RATING_STAR'];
                                $noOfUsers++;
                            }

                            if($noOfUsers>0){
                                $averageRating=$averageRating/$noOfUsers;
                            }

                            if($averageRating>=$ratingLimit){
                                $productsDisplayed++;

                                echo "<div class='product'>";
                                echo "<div class='product-image'>"; 

                                if($product['DISCOUNT']>0){
                                    echo "
                                    <span class='product-discount'>
                                        $product[DISCOUNT]%
                                    </span>";
                                }
                                
                                echo "
                                <a href='../productDetails/productDetails.php?productId=$product[PRODUCT_ID]'>
                                    <img src='$product[PRODUCT_IMAGE]' alt='$product[PRODUCT_NAME]'>
                                </a>
                                ";
        
                                echo "</div>";
        
                                echo "
                                    <div class='product-description'>
                                        <a style='text-decoration: none;' href='../productDetails/productDetails.php?productId=$product[PRODUCT_ID]'>
                                            <p class='product-name'>$product[PRODUCT_NAME]</p>
                                            <p class='product-price'>&pound;"
                                            .($product['PRODUCT_PRICE']-($product['DISCOUNT']/100*$product['PRODUCT_PRICE'])).
                                            "</p>
                                        </a>
                                ";
                                echo "<span class='rating'>";
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

                                echo "</span>";
                                // display default add to cart if product not in cart
                                if(!$productInCart && sizeof($_SESSION['currentCart'])<20){
                                    echo "
                                    <a href='./defaultCart.php?productId=$product[PRODUCT_ID]'>
                                        <svg class='cart-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M24 3l-.743 2h-1.929l-3.474 12h-13.239l-4.615-11h16.812l-.564 2h-13.24l2.937 7h10.428l3.432-12h4.195zm-15.5 15c-.828 0-1.5.672-1.5 1.5 0 .829.672 1.5 1.5 1.5s1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5zm6.9-7-1.9 7c-.828 0-1.5.671-1.5 1.5s.672 1.5 1.5 1.5 1.5-.671 1.5-1.5c0-.828-.672-1.5-1.5-1.5z'/></svg>
                                    </a>
                                    ";
                                }

                                echo "</div>";
        
                                echo "
                                    <div class='trader'>
                                        <div class='trader-name'>$product[USER_NAME]</div>
                                    </div>
                                ";
        
        
                                echo "</div>";
                            }

                        }

                    }

                    if($productsDisplayed==0){
                        echo "<h3>Products with the specific properties not found.</h3>";
                    }
                }
            
            ?>
        
        </div>


        <div class="search-container">

            <form method="GET">
            
                <div class="input-search">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z"/></svg>
                    <input type="text" placeholder="Search..." value="<?php echo isset($_SESSION['searchData'])?$_SESSION['searchData']:''; ?>" name="product_name">
                </div>

                <select name="product-type" id="product-dropdown">
                    <option selected value=''>Product Category</option>
                    <?php
                        $traderQuery = "SELECT category_type FROM HAMROMART.trader_category";
                        $traderResult = oci_parse($connection, $traderQuery);
                        oci_execute($traderResult);

                        while($key = oci_fetch_assoc($traderResult)) {
                            $value = $key['CATEGORY_TYPE'];
                            if (isset($_SESSION['traderCategory']) && $_SESSION['traderCategory'] == $value){
                                echo "<option value ='$value' selected )>$value</option>";
                            }
                            else{
                                echo "<option value ='$value'>$value</option>";
                            }                            
                        }
                    ?>

                </select>

                <select name="shop" id="shop-dropdown">
                    <option selected value=''>Shop Name</option>

                    <?php
                        $shopQuery = "SELECT shop_name FROM HAMROMART.shop";
                        $shopResult = oci_parse($connection, $shopQuery);
                        oci_execute($shopResult);

                        while($key=oci_fetch_assoc($shopResult)) {
                            $value = $key['SHOP_NAME'];
                            if (isset($_SESSION['shopName']) && $_SESSION['shopName'] == $value ) {
                                echo "<option value ='$value' selected>$value</option>";
                            }
                            else{
                                echo "<option value ='$value'>$value</option>";
                            }
                            
                        }
                    ?>
                </select>

                <div class="price-filter">
                     <input type="number" name="min_price" placeholder="Min Price" min="0" value="<?php 
                        if(isset($_SESSION['minData'])){echo $_SESSION['minData']; unset($_SESSION['minData']);} ?>">
                    <input type="number" name="max_price" placeholder="Max Price" min="0" value="<?php 
                        if(isset($_SESSION['maxData'])){ echo $_SESSION['maxData']; unset($_SESSION['maxData']);} ?>">
                </div>

                <select class="rating-filter" name="rating" id="">
                    <option selected value="0">Rating</option>

                    <option value="1" <?php if (isset($_SESSION['rateData']) && $_SESSION['rateData'] == '1') {
                        echo "selected"; unset($_SESSION['rateData']);
                    } ?> >1+</option>
                    <option value="2" <?php if (isset($_SESSION['rateData']) && $_SESSION['rateData'] == '2') {
                        echo "selected"; unset($_SESSION['rateData']);
                    } ?> >2+</option>
                    <option value="3" <?php if (isset($_SESSION['rateData']) && $_SESSION['rateData'] == '3') {
                        echo "selected"; unset($_SESSION['rateData']);
                    } ?> >3+</option>
                    <option value="4" <?php if (isset($_SESSION['rateData']) && $_SESSION['rateData'] == '4') {
                        echo "selected"; unset($_SESSION['rateData']);
                    } ?> >4+</option>
                    <option value="5" <?php if (isset($_SESSION['rateData']) && $_SESSION['rateData'] == '5') {
                        echo "selected"; unset($_SESSION['rateData']);
                    } ?> >5+</option>

                </select>

                <input type="submit" value="Submit" class="submit-btn" name="submit">

            </form>
        
        </div>
    
    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>