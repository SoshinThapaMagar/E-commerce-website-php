<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="invoice.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <title>Order Details</title>
</head>
<body>


    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        $orderId = $_GET['order_id'];
        if(!$orderId || !$_SESSION['userId']){
            header('Location: ./orderDetails.php');
        }

        $customerName= $orderDate= $collectionSlot= '';
        $discountPercent=0;
        $totalPrice=0;
        $orderExists = false;

        $detailsQuery = "
            SELECT 
            o.order_date, u.user_name, cs.collection_day, cs.collection_time, co.discount_percent
            FROM HAMROMART.orders o
            INNER JOIN HAMROMART.collection_slot cs ON o.slot_id=cs.slot_id
            INNER JOIN HAMROMART.cart c ON c.cart_id = o.cart_id
            INNER JOIN HAMROMART.users u ON u.user_id = c.user_id
            LEFT OUTER JOIN HAMROMART.coupon co ON co.coupon_id = o.coupon_id
            WHERE o.order_id = $orderId AND u.user_id=$_SESSION[userId]
        ";
        $detailsResult = oci_parse($connection, $detailsQuery);
        oci_execute($detailsResult);

        if($detailsResult){
            while($detail=oci_fetch_assoc($detailsResult)){
                $orderExists=true;

                $customerName = $detail['USER_NAME'];
                $orderDate = $detail['ORDER_DATE'];
                $collectionSlot = $detail['COLLECTION_DAY']." ".$detail['COLLECTION_TIME'];
                $discountPercent=$detail['DISCOUNT_PERCENT'];

            }
        }

        if(!$orderExists){
            include '../401/401.php';
            exit();
        }

    ?>


    <div class="invoice-container">

        <div class="invoice-header">

            <div class="header-logo">
                <img src="../images/logo-black.png" alt="">
            </div>

            <div class="header-date">
                Date Issued: <?php echo $orderDate; ?>
            </div>

        </div>

        <div class="invoice-details">
            <h3>Order ID: <?php echo $orderId; ?></h3>
            <h3>Customer Name: <?php echo $customerName; ?></h3>
            <h3>Collection Slot: <?php echo $collectionSlot; ?></h3>
        </div>

        <div class="order-details">

            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Product Quantity</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    
                        $orderQuery = "
                        SELECT 
                        od.product_quantity, od.product_id, p.product_price, p.product_name, p.discount
                        FROM HAMROMART.order_details od
                        INNER JOIN HAMROMART.orders o ON o.order_id = od.order_id
                        INNER JOIN HAMROMART.product p ON p.product_id = od.product_id
                        WHERE o.order_id=$orderId
                        ";

                        $orderQueryResult = oci_parse($connection, $orderQuery);
                        oci_execute($orderQueryResult);

                        if($orderQueryResult){

                            while($orderDetail=oci_fetch_assoc($orderQueryResult)){

                                $calculatedPrice = ($orderDetail['PRODUCT_PRICE']-(($orderDetail['DISCOUNT']/100)*$orderDetail['PRODUCT_PRICE']))*$orderDetail['PRODUCT_QUANTITY'];
                                $totalPrice+=$calculatedPrice;

                                echo "<tr>";

                                echo "<td>
                                    $orderDetail[PRODUCT_NAME]
                                </td>";

                                echo "<td>
                                    &pound; $orderDetail[PRODUCT_PRICE]
                                </td>";

                                echo "<td>
                                    $orderDetail[PRODUCT_QUANTITY]
                                </td>";

                                echo "<td>
                                    $orderDetail[DISCOUNT]
                                </td>";

                                echo "<td class='total-price'>
                                    &pound; $calculatedPrice
                                </td>";

                                echo "</tr>";
                            }

                            // discounted price
                            $totalPrice = ($totalPrice-($discountPercent/100)*$totalPrice);
                        }
                    
                    ?>
                </tbody>

            </table>

        </div>

        <div class="order-summary">
            <h3>Coupon Discount: <?php echo $discountPercent; ?></h3>
            <h2>Total: &pound; <?php echo $totalPrice; ?></h2>
        </div>

    </div>

    <?php
        
        include '../footer/footer.php';

    ?>

    <script src="../navbar/navbar.js"></script>
    
</body>
</html>