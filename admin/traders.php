<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traders</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="traders.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
        include '../init.php';

        if(!isset($_SESSION['userId'])||(isset($_SESSION['userRole']) && $_SESSION['userRole']!='admin')){
            include '../401/401.php';
            exit();
        }
    
    ?>

    <div class="traders-container">

        <h2 class="container-title">
            New Trader Requests
        </h2>
 

        <div class="traders">
            <?php
                if(isset($_SESSION['traderVerificationSuccess'])){
                    echo "<p class='success-message'>$_SESSION[traderVerificationSuccess]</p>";
                    unset($_SESSION['traderVerificationSuccess']);
                }

            ?>
            <?php
            
                $tradersQuery = "
                    SELECT u.user_id, u.user_name, t.category_type FROM HAMROMART.users u
                    INNER JOIN HAMROMART.trader_category t ON t.category_id = u.category_id 
                    WHERE lower(user_role)='trader' AND lower(verified)='false'
                ";
                $tradersQueryResult = oci_parse($connection, $tradersQuery);

                oci_execute($tradersQueryResult);
                if($tradersQueryResult){
                    $noOfTraders = 0;

                    while($trader = oci_fetch_assoc($tradersQueryResult)){
                        $noOfTraders++;

                        echo "<div class='trader'>";
                        
                        echo "<span class='trader-id'>$trader[USER_ID]</span>";
                        echo "
                            <div class='trader-info'/>
                                <p class='trader-name'>$trader[USER_NAME]</p>
                                <p class='trader-category'>$trader[CATEGORY_TYPE]</p>
                            </div>
                        ";

                        echo "
                        <a href='./verifyTrader.php?trader_id=$trader[USER_ID]'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M9 21.035l-9-8.638 2.791-2.87 6.156 5.874 12.21-12.436 2.843 2.817z'/></svg>
                        </a>
                        ";

                        echo "</div>";
                    }

                    if($noOfTraders==0){
                        echo "<h3>No new trader requests.</h3>";
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