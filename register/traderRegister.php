<?php
include '../init.php';


// declaring the variable
$errors = array();
$successs=array(); 
$user_role="Trader";


// register the trader
if (isset($_POST['signup'])) {
  // receive all form input values
  $username =$_POST['username'];
  $email =$_POST['email'];
  $password =$_POST['password'];
  $cpassword = $_POST['cpassword'];
  $phonenumber = $_POST['number'];
  $tradertype = $_POST['tradertype'];
  


  //form validation: check that the form is filled out properly
  // by adding (array_push()) corresponding error into $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password)) { array_push($errors, "Password is required"); }
  if (empty($phonenumber)) { array_push($errors, "Phonenumber is required"); }
  if (empty($tradertype)) { array_push($errors, "Tradertype is required"); }
  
//making the data persist
if (!empty($username)) {$_SESSION['usernameData'] = $username;}
if (!empty($email)) {$_SESSION['emailData'] = $email;}
if (!empty($password)) {$_SESSION['passwordData'] = $password;}
if (!empty($cpassword)) {$_SESSION['cpasswordData'] = $cpassword;}
if (!empty($phonenumber)) {$_SESSION['phonenumberData'] = $phonenumber;}

  if ($password != $cpassword) {
	array_push($errors, "Password do not match");
  }

  
  //there isn't another customer with the same username,email address and phone number
  $userEmailCheck = "SELECT * FROM HAMROMART.USERS WHERE USER_EMAIL='$email'";
  $userPhoneNumberCheck = "SELECT * FROM HAMROMART.USERS WHERE USER_PHONE_NUMBER='$phonenumber'";
  
  $userEmailCheckResult = oci_parse($connection,$userEmailCheck);
  $userPhoneNumberCheckResult = oci_parse($connection, $userPhoneNumberCheck);
  oci_execute($userEmailCheckResult);
  oci_execute($userPhoneNumberCheckResult);

  if($userEmailResultAns= oci_fetch_assoc($userEmailCheckResult)){
  
    array_push($errors, 'Email address already exists.');
     }
        
     if($userPhoneNumberResultAns=oci_fetch_assoc($userPhoneNumberCheckResult)){
      array_push($errors, 'Phone number already exists.'); 
     }

  //password stength
  if(strlen($password) <=8)
  {
      $errors['password'] = "Your Password Must Contain At Least 8 Characters";
  }
  elseif(!preg_match('#[0-9]+#',$password))
  {
      $errors['password']="Your Password Must Contain At Least 1 Capital Letter!";
  }
  elseif(!preg_match('#[A-Z]+#',$password))
  {
      $errors['password']="Your Password Must Contain At Least 1 Capital Letter!";
  }
  elseif(!preg_match('#[a-z]+#',$password))
  {
      $errors['password']="Your Password Must Contain At Least 1 Lowercase Letter!";
  }
  elseif(!preg_match('@[^\w]@', $password))
  {
      $errors['password']="Your Password Must Contain At Least 1 Special Character!";
  }

  // register the trader if there are no errors in the registration form
  if (count($errors) == 0) {
  	$password = md5($cpassword);//encrypt the password before storing it in the database.

  	$query = "INSERT INTO HAMROMART.USERS (USER_PHONE_NUMBER,USER_NAME, USER_EMAIL, USER_PASSWORD,USER_ROLE,CATEGORY_ID) 
  			  VALUES('$phonenumber','$username', '$email', '$password','$user_role', '$tradertype')";
      
    
  	$queryResult = oci_parse($connection,$query);
    oci_execute($queryResult);

    if($queryResult){
        array_push($successs, "Registration successful.");        
    }

  
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register as Trader</title>
    <link rel="stylesheet" href="../navbar/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <?php

        include '../navbar/navbar.php';
    
    ?>

    <div class="register-container">
    
        <div class="form-container">

            <a href="customerRegister.php" class="login-page-link">Register as Customer</a>
            <a href="" class="login-page-link active-login-link">Register as Trader</a>
            
            <form action="traderRegister.php" method="POST">
            <?php include 'errors.php'; 
            if (isset($_SESSION['emailSent'])) {
                echo $_SESSION['emailSent'];
                unset($_SESSION['emailSent']);
              }
              elseif(isset($_SESSION['emailFail'])) {
            		echo $_SESSION['emailFail'];
            		unset($_SESSION['emailFail']);
            	}

              if (isset($_SESSION['register_error'])) {
                echo $_SESSION['register_error'];
                unset($_SESSION['register_error']);
              }elseif (isset($_SESSION['successful_update'])) {
                echo $_SESSION['successful_update'];
                unset($_SESSION['successful_update']);
              }
              ?>
                <h2>Create a new Trader account</h2>
               
                <input type="text" name="username" id="" placeholder="Trader Name">
                <input type="text" name="email" id="" placeholder="Email Address">
                <input type="password" name="password" id="" placeholder="Password">
                <input type="password" name="cpassword" id="" placeholder="Confirm Password">
                <input type="number" name="number" id="" placeholder="Phone Number">
                <select name="tradertype" id="">
                <option selected disabled>Select Your Trader Type</option>
                    
                    <?php
                    
                    $traderQuery="Select * FROM HAMROMART.TRADER_CATEGORY";
                    $traderQueryResult= oci_parse($connection, $traderQuery);
                    oci_execute($traderQueryResult);
                    while($key=oci_fetch_assoc($traderQueryResult)){
                        $value=$key['CATEGORY_ID'];
                        echo "<option value ='$value'>$key[CATEGORY_TYPE]</option>";
                    }
                   
                   
                    ?>
                </select>
               
                <input class="submit-btn" name="signup" type="submit" value="Request a Trader Account">

                <a href="../login/customerLogin.php" class="login-link">Already have an account? <span>Log In Here</span></a>
            
            </form>
        
        </div>
    
    </div>

    <?php
    
        include '../footer/footer.php';
    
    ?>

    <script src="../navbar/navbar.js"></script>

    
</body>
</html>