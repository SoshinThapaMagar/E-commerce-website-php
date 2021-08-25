<?php
    include './altertUser.php';
    include '../init.php';

    if(isset($_POST['submit_btn'])){

        $userName = $_POST['user_name'];
        $userPhoneNumber = $_POST['user_phone_number'];

        if(empty($userName)){
            $_SESSION['profileNameError']='Your name must not be empty.';
            header('Location: ./settings.php');
            return;
        }
        else{
            unset($_SESSION['profileNameError']);
        }

        if(empty($userPhoneNumber)){
            $_SESSION['profilePhoneError']='Your phone number must not be empty.';
            header('Location: ./settings.php');
            return;
        }
        else{
            unset($_SESSION['profilePhoneError']);
        }

        //checking unique phone number
        $phoneNumberQuery = "
            SELECT user_id FROM HAMROMART.users WHERE user_phone_number = $userPhoneNumber
        ";
        $phoneNumberQueryResult = oci_parse($connection, $phoneNumberQuery);
        oci_execute($phoneNumberQueryResult);

        $phoneNumberExists = false;
        while($phoneNumber = oci_fetch_assoc($phoneNumberQueryResult)){
            if($phoneNumber['USER_ID']!==$_SESSION['userId']){
                $phoneNumberExists = true;
            }
        }
        if($phoneNumberExists){
            $_SESSION['profilePhoneError']='Your phone number must be unique.';
            header('Location: ./settings.php');
            return;
        }

        //update profile
        $updateQuery = "
            UPDATE HAMROMART.users SET user_name='$userName', user_phone_number='$userPhoneNumber' 
            WHERE user_id=$_SESSION[userId]
        ";
        $updateQueryResult = oci_parse($connection, $updateQuery);
        oci_execute($updateQueryResult);
        if($updateQueryResult){
            $_SESSION['profileUpdateSuccess'] = 'Profile Updated!';
            // header('Location: ./settings.php');
            sendMail('Profile');
        }

    }
    else{
        header('Location: ./settings.php');
    }

?>