<?php
include '../init.php';
$user_id=$_SESSION['userId'];

if($connection){
	if(isset($_POST['submit'])){

		if(empty($_POST['shop_name'])){
			$_SESSION['error']="name";
			header('location:'.$_SESSION['url']);
			unset($_SESSION['url']);
			exit();
		}else{
			$shop_name=$_POST['shop_name'];
		}

        $shopNumberQuery = "SELECT COUNT(shop_id) number_of_shops FROM HAMROMART.shop WHERE user_id=$_SESSION[userId]";
        $shopNumberQueryResult = oci_parse($connection, $shopNumberQuery);
        oci_execute($shopNumberQueryResult);

        if($shopNumberQueryResult){
            $numberOfShops=0;

            while($shopNumber = oci_fetch_assoc($shopNumberQueryResult)){
                $numberOfShops=(int)$shopNumber['NUMBER_OF_SHOPS'];
            }

            if($numberOfShops>=10){
                $_SESSION['shopLimitError']="You have reached the maximum limit of 10 shops.";
                header('Location: ./addShop.php');
            }
            else{
                $traderEmail='';
                $traderEmailQuery = "SELECT user_email FROM HAMROMART.users WHERE user_id=$_SESSION[userId]";
                $traderEmailQueryResult = oci_parse($connection, $traderEmailQuery);
                oci_execute($traderEmailQueryResult);

                if($traderEmailQueryResult){
                    while($user = oci_fetch_assoc($traderEmailQueryResult)){
                        $traderEmail=$user['USER_EMAIL'];
                    }

                    $sql="INSERT INTO HAMROMART.shop(shop_name,user_id,permissions) VALUES ('$shop_name',$user_id,'$traderEmail')";
                    $query=oci_parse($connection,$sql);
                    oci_execute($query);

                    if($query){
                        $_SESSION['status']="success";
                        header('location:'.$_SESSION['url']);
                        unset($_SESSION['url']);
                        exit();
                    }else{
                        $_SESSION['status']="fail";
                        header('location:'.$_SESSION['url']);
                        unset($_SESSION['url']);
                        exit();
                    }
                }
                
            }
        }

	}
}
?>