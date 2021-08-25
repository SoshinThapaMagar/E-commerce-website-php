<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="updateProduct.css">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="addProduct.css">
    <title>Add Product</title>
</head>
<body>

    <?php
        include '../navbar/navbar.php';
        if(!isset($_SESSION['userRole']) || $_SESSION['userRole']!='trader'){
            include '../401/401.php';
            exit();
        }
    ?>
    <?php
        include '../init.php';
        $_SESSION['url']="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    ?>

    <div class="add-container">
    
        <div class="form-container">

            <h2 class="container-title">
                Add New <br>
                Product...
            </h2>

            <!-- add method to this form and name attributes to the inputs -->
            <form action="add.php" method="POST">

                <!-- checking if the current user has any shops, open or not. if no shop is found, display error message -->
                <?php

                    $noOfShops=0;
                    $userShopQuery = "SELECT COUNT(shop_id) SHOP_NUMBER FROM HAMROMART.shop WHERE user_id=$_SESSION[userId]";
                    $userShopQueryResult = oci_parse($connection, $userShopQuery);
                    oci_execute($userShopQueryResult);

                    if($userShopQueryResult){
                        while($shop=oci_fetch_assoc($userShopQueryResult)){
                            $noOfShops=(int)$shop['SHOP_NUMBER'];
                        }
                    }

                    if($noOfShops==0){
                        echo "<h3>You currently do not own a shop. Please open a shop before adding products.</h3>";
                        echo "<a href='../shopCRUD/addShop.php' class='add-shop-button'>Open Shop</a>";
                        exit();
                    }
                
                ?>

                <!-- displaying prodcut errors -->
                <?php
                
                    if(isset($_SESSION['productErrors']) && sizeof($_SESSION['productErrors'])>0){
                        echo "<div class='product-errors'>";

                        foreach($_SESSION['productErrors'] as $error){
                            echo "<p>$error</p>";
                        }

                        echo "</div>";

                        unset($_SESSION['productErrors']);
                    }

                
                ?>

                <input type="text" placeholder="Product Name" name="product_name"
                    value="<?php echo isset($_SESSION['productAddName'])?$_SESSION['productAddName']:''; unset($_SESSION['productAddName']); ?>"
                >
                <?php 
                        if(isset($_SESSION['error'])){
                            if($_SESSION['error']=="name"){
                                echo "please fill in the product name";
                                unset($_SESSION['error']);
                             }
                         }
                    ?>
                <textarea name="description" id="" cols="30" rows="5" placeholder="Product Description"><?php echo isset($_SESSION['productAddDescription'])?$_SESSION['productAddDescription']:''; unset($_SESSION['productAddDescription']); ?></textarea>
                <?php 
                        if(isset($_SESSION['error'])){
                            if($_SESSION['error']=="description"){
                                echo "product description should not be empty.";
                                unset($_SESSION['error']);
                             }
                         }
                    ?>
                
                <div class="product-price">
                    <input type="number" placeholder="Product Price" name="price" min="1"
                        value="<?php echo isset($_SESSION['productAddPrice'])?$_SESSION['productAddPrice']:''; unset($_SESSION['productAddPrice']); ?>"
                    >
                    <?php 
                        if(isset($_SESSION['error'])){
                            if($_SESSION['error']=="price"){
                                echo "product price should not be empty.";
                                unset($_SESSION['error']);
                             }
                         }
                    ?>

                    <input type="number" placeholder="Discount" name="discount" max="100" min="0">

                </div>

                <input type="text" placeholder="stock quantity" name="stock"
                    value="<?php echo isset($_SESSION['productAddPrice'])?$_SESSION['productAddPrice']:''; unset($_SESSION['productAddPrice']); ?>"
                >

                <textarea name="allergy_info" id="" cols="30" rows="5" placeholder="Allergy Information"><?php echo isset($_SESSION['productAddAllergy'])?$_SESSION['productAddAllergy']:''; unset($_SESSION['productAddAllergy']); ?></textarea>

                <input type="text" placeholder="Image Link" name="image">


                <select name="shop" id="">
                	<?php

                	echo '<option>'.'Select a shop to display the product'.'</option>';
                	include '../init.php';

                	$id=$_SESSION['userId'];
					$sql="SELECT * FROM HAMROMART.shop WHERE user_id=$id";
					$result=oci_parse($connection,$sql);
                    oci_execute($result);

					while($row=oci_fetch_assoc($result)){

					    echo'<option value="'.$row['SHOP_ID'].'">'.$row['SHOP_NAME'].'</option>';

                    }
                	?>
                	

                </select>

                <div class="minMax">
                	<input type="number" placeholder="Minimum order" name="minOrder" min="1">
                	<input type="number" placeholder="Maximum order" name="maxOrder" max="20">
                </div>

                <input class="add-button" type="submit" value="Add New Product" name="product_submit">           
            
            </form>
            <?php
           		if(isset($_SESSION['status'])){
            		if($_SESSION['status']=="successfull"){

                		echo "<p class='success-message'>Product added successfully.</p>";
                		unset($_SESSION['status']);

            		}
        		}
           ?>

        </div>

    </div>

    <?php
        include '../footer/footer.php';
    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>