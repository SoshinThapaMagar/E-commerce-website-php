<?php
	include '../init.php';
	if (isset($_GET['key'])) {
		$email = $_GET['key'];
		$query = "SELECT user_email, verified, user_role FROM HAMROMART.USERS WHERE user_email = '$email' AND verified = 'FALSE' AND user_role = 'Customer'";
		$checkemail = oci_parse($connection, $query);
		oci_execute($checkemail);

		if ($result = oci_fetch_assoc($checkemail)) {
			$update = "UPDATE HAMROMART.USERS SET verified = 'TRUE' WHERE user_email = '$email'";
			$setUpdate = oci_parse($connection, $update);
			oci_execute($setUpdate);

			if ($setUpdate) {
				$_SESSION['successful_update'] = 'Your "Customer" account has been verified. You may login.';
				header("location:../login/customerLogin.php");
			}else{
				$_SESSION['register_error'] = "Error, fault in query.";
				header("location:customerRegister.php");
			}

		}else{
			$_SESSION['register_error'] = "Error, your account has already been verified.";
			header("location:../login/customerLogin.php");
		}
	}else{
		$_SESSION['register_error'] = "You do not have a key.";
		header("location:customerRegister.php");
	}
?>