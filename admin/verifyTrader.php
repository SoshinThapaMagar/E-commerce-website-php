<?php

    include '../init.php';
    $traderId = $_GET['trader_id'];

    if(!$traderId){
        header('Location: ./traders.php');
    }
    else{

        $verifyTraderQuery = "
            UPDATE HAMROMART.users SET verified='TRUE'
            WHERE user_id=$traderId
        ";
        $verifyTraderQueryResult = oci_parse($connection, $verifyTraderQuery);
        oci_execute($verifyTraderQueryResult);

        if($verifyTraderQueryResult){
            $traderEmail='';
            $traderName = '';
            $traderEmailQuery = "SELECT user_email, user_name FROM HAMROMART.users WHERE user_id=$traderId";
            $traderEmailQueryResult = oci_parse($connection, $traderEmailQuery);

            oci_execute($traderEmailQueryResult);
            if($traderEmailQueryResult){
                while($trader = oci_fetch_assoc($traderEmailQueryResult)){
                    $traderEmail = $trader['USER_EMAIL'];
                    $traderName = $trader['USER_NAME'];
                }

                // send email to trader
                $to = $traderEmail;
                $subject = "Account Verification";
                $message = "Hi $traderName, <br>
                Your trader account has been successfully verified! You can now login to your account. <br>
                You can use the following credentails to login to your database: <br>
                Email: $traderEmail <br>
                Password: trader123 <br>
                Plesae change your password after your initial login for security measures. <br>
                Regards:Hamro-Mart";
                $headers = "From: HamroMart \r\n";
                $headers .= "MIME-Version: 1.0"."\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['traderVerificationSuccess']='Trader Verified Successfully!';
                    header('Location: ./traders.php');
                }
            }
        }
        else{
            header('Location: ./traders.php');
        }

    }


?>