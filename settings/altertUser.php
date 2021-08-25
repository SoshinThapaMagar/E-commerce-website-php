<?php

    function sendMail($setting){
        include '../init.php';
        
        $userEmailQuery = "SELECT user_email, user_name FROM HAMROMART.users WHERE user_id=$_SESSION[userId]";
        $userEmailQueryResult = oci_parse($connection, $userEmailQuery);
        oci_execute($userEmailQueryResult);

        if($userEmailQueryResult){
            while($user = oci_fetch_assoc($userEmailQueryResult)){
                $userEmail = $user['USER_EMAIL'];
                $userName = $user['USER_NAME'];
            }

            $to = $userEmail;
            $subject = "Account Changed!";
            $message = "Hi $userName,\r\n 
            Your $setting was recently updated. If this was not done by you, please feel free to contact us. \n
            Regards: Hamro-Mart";
            $headers = "From: HamroMart \r\n";
            $headers .= "MIME-Version: 1.0"."\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";

            if (mail($to, $subject, $message, $headers)) {
                header('Location: ./settings.php');
            }
            else{
                header('Location: ./settings.php');
            }
        }

    }

?>