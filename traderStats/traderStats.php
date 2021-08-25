<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="traderStats.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <title>Your Stats</title>
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        if(!isset($_SESSION['userRole']) || $_SESSION['userRole']!='trader'){
            include '../401/401.php';
            exit();
        }

        $noOfProducts=0;
        $noOfShops=0;
    
    ?>

    <div class="container">

        <div class="shop-container">

            <div class="table-headers">
                <h2 class="container-title">Your Shops...</h2>
                <form method="GET">
                    <input type="text" name="shop_search" placeholder="Search Shop...">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z"/></svg>
                    </button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <td># Shop id</td>
                        <td>Shop Name</td>
                        <td>Shop Orders</td>
                        <td>Shop Products</td>
                        <td>Edit</td>
                    </tr>
                </thead>

                <tbody>


                    <?php

                        $shopSearch=(isset($_GET['shop_search'])&&!empty($_GET['shop_search']))?$_GET['shop_search']:'';
                        $shopQuery = "
                            SELECT 
                                s.shop_id, s.shop_name, COUNT(distinct p.product_id) total_products, COUNT(distinct o.order_id) total_orders
                                FROM HAMROMART.shop s
                                INNER JOIN HAMROMART.users u ON s.user_id = u.user_id
                                LEFT OUTER JOIN HAMROMART.product p ON p.shop_id=s.shop_id
                                LEFT OUTER JOIN HAMROMART.order_details od ON od.product_id = p.product_id 
                                LEFT OUTER JOIN HAMROMART.orders o ON od.order_id = o.order_id
                            WHERE u.user_id = $_SESSION[userId] AND lower(s.shop_name) LIKE lower('%$shopSearch%')
                            GROUP BY s.shop_id, s.shop_name
                        ";

                        $shopQueryResult = oci_parse($connection, $shopQuery);
                        oci_execute($shopQueryResult);

                        if($shopQueryResult){
    
                            while($shop = oci_fetch_assoc($shopQueryResult)){
                                $noOfShops++;
                                echo "<tr>";
                                echo "<td>$shop[SHOP_ID]</td>";
                                echo "<td>$shop[SHOP_NAME]</td>";
                                echo "<td>$shop[TOTAL_ORDERS]</td>";
                                echo "<td>$shop[TOTAL_PRODUCTS]</td>";
                                echo "
                                <td>
                                    <a href='../shopCRUD/updateShop.php?shop_id=$shop[SHOP_ID]'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M1.438 16.873l-1.438 7.127 7.127-1.437 16.874-16.872-5.69-5.69-16.873 16.872zm1.12 4.572l.722-3.584 2.86 2.861-3.582.723zm18.613-15.755l-13.617 13.617-2.86-2.861 13.617-13.617 2.86 2.861z'/></svg>
                                    </a>                                    
                                </td>";
                                echo "</tr>";

                            }
                        }
                    
                    ?>
                </tbody>
            </table>

            <?php
                if($noOfShops==0){
                    echo "<h3>You do not own any shops.</h3>";
                }
            ?>

        </div>


        <div class="product-container">
        <div class="table-headers">
                <h2 class="container-title">Your Products...</h2>
                <form method="GET">
                    <input type="text" name="product_search" placeholder="Search Product...">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z"/></svg>
                    </button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <td>
                            # Product ID
                        </td>
                        <td>Product Name</td>
                        <td>Shop Name</td>
                        <td>Product Price</td>
                        <td>Product Stock</td>
                        <td>Edit</td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        $productSearch=(isset($_GET['product_search'])&&!empty($_GET['product_search']))?$_GET['product_search']:'';

                        $productsQuery = "
                            SELECT p.product_id, p.product_name, s.shop_name, p.product_price, p.stock
                            FROM HAMROMART.product p
                            LEFT OUTER JOIN HAMROMART.shop s ON p.shop_id=s.shop_id
                            LEFT OUTER JOIN HAMROMART.users u ON u.user_id = s.user_id
                            WHERE u.user_id = $_SESSION[userId] AND lower(p.product_name) LIKE lower('%$productSearch%')
                        ";
                        $productsQueryResult = oci_parse($connection, $productsQuery);
                        oci_execute($productsQueryResult);

                        
                        if($productsQueryResult){
                            while($product=oci_fetch_assoc($productsQueryResult)){
                                $noOfProducts++;

                                echo "<tr>";
                                echo "<td>$product[PRODUCT_ID]</td>";
                                echo "<td>$product[PRODUCT_NAME]</td>";
                                echo "<td>$product[SHOP_NAME]</td>";
                                echo "<td>$product[PRODUCT_PRICE]</td>";
                                echo "<td>$product[STOCK]</td>";
                                echo "<td>
                                    <a href='../productCRUD/updateProduct.php?product_id=$product[PRODUCT_ID]'>
                                        <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M1.438 16.873l-1.438 7.127 7.127-1.437 16.874-16.872-5.69-5.69-16.873 16.872zm1.12 4.572l.722-3.584 2.86 2.861-3.582.723zm18.613-15.755l-13.617 13.617-2.86-2.861 13.617-13.617 2.86 2.861z'/></svg>
                                    </a>
                                    </td>";
                                echo "</tr>";

                            }
                        }
                    
                    ?>

                </tbody>
            </table>

            <?php
                if($noOfProducts==0){
                    echo "<h3>You do not own any products.</h3>";
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