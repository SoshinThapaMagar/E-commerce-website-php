<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link rel="stylesheet" href="orderDetails.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <title>Your Orders</title>
</head>
<body>


    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        if((isset($_SESSION['userRole']) && $_SESSION['userRole']!='customer')){
            include '../401/401.php';
            exit();
        }

    ?>


    <div class="order-container">

        <h2 class="container-title">
            My Orders
        </h2>

        <div class="orders">

            <?php

                if($_SESSION['userId']){
                    $ordersQuery = "
                    SELECT 
                    o.order_id, o.order_date, o.delivered 
                    FROM HAMROMART.orders o
                    INNER JOIN HAMROMART.cart c ON c.cart_id = o.cart_id
                    INNER JOIN HAMROMART.users u ON u.user_id = c.user_id
                    WHERE u.user_id=$_SESSION[userId]
                    ORDER BY o.order_id DESC
                    ";
                    $ordersQueryResult = oci_parse($connection, $ordersQuery);
                    oci_execute($ordersQueryResult);

                    if($ordersQueryResult){
                        $noOfOrders = 0;

                        while($order=oci_fetch_assoc($ordersQueryResult)){
                            $noOfOrders++;
                            
                            echo "
                                <div class='order'>
                                    <a href='./invoice.php?order_id=$order[ORDER_ID]'>
                                        <h2>
                                            Order #$order[ORDER_ID]
                                        </h2>
                                        <span>
                                            Date: $order[ORDER_DATE]
                                        </span>
                                    </a>
                            ";

                            if(strtolower($order['DELIVERED'])=='true'){
                                echo "<svg class='delivered-order' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-1.959 17l-4.5-4.319 1.395-1.435 3.08 2.937 7.021-7.183 1.422 1.409-8.418 8.591z'/></svg>";
                            }

                            echo "</div>";

                        }

                        if($noOfOrders==0){
                            echo "<h3>No Orders Found.</h3>";
                        }
                    }
                    
                }
                else{
                    echo "<h3>Please log in to see your order history.</h3>";
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