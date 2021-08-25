<?php
include '../init.php';
$shop_id=$_GET['shop_id'];
if($connection){
if(isset($_POST['submit'])){
	if(empty($_POST['shop_name'])){
		$_SESSION['error']="name";
		header('location:'.$_SESSION['url']);
		unset($_SESSION['url']);
		exit();
	}
	else{
		$shop_name=$_POST['shop_name'];
	}

	$sql="UPDATE HAMROMART.shop SET shop_name='$shop_name' WHERE shop_id=$shop_id";
	$query=oci_parse($connection,$sql);
    oci_execute($query);

	if($query){
		$_SESSION['status']="success";
		header('location:'.$_SESSION['url']);
		exit();
	}
	else{
		$_SESSION['status']="fail";
		header('location:'.$_SESSION['url']);
		exit();

	}

}

if(isset($_POST['delete_submit'])){
	$sql1="DELETE FROM HAMROMART.shop WHERE shop_id=$shop_id";

	$query1=oci_parse($connection,$sql1);
    oci_execute($query1);

	if($query1){
		$_SESSION['status']="delete";
        header('location: ../traderStats/traderStats.php');
		unset($_SESSION['url']);
		exit();
	}
}
}


?>