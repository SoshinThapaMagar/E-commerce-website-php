<?php
include '../init.php';


if($connection){

    $_SESSION['productErrors'] = array();

	if(isset($_POST['product_submit'])){

		//storing product name
		if($_POST['product_name'] != null && $_POST['product_name'] != " "){
			$name=htmlentities($_POST['product_name']);
            $_SESSION['productAddName'] = $name;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the product name.");
		}

		if($_POST['description']!= null && $_POST['description']!= " "){
			$description = htmlentities($_POST['description']);
            $_SESSION['productAddDescription'] = $description;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the product description.");
		}

        if($_POST['price']!= null && $_POST['price']!=" "){
			$price = htmlentities($_POST['price']);
            $_SESSION['productAddPrice'] = $price;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the product price.");
		}

        if($_POST['allergy_info']!= null && $_POST['allergy_info']!=" "){
			$allergy_info = htmlentities($_POST['allergy_info']);
            $_SESSION['productAddAllergy'] = $allergy_info;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the allergy information.");
		}

        if($_POST['shop']!= null && $_POST['shop']!=" "){
			$shop =htmlentities($_POST['shop']);
            $_SESSION['productAddShop'] = $shop;
		}else{
			array_push($_SESSION['productErrors'], "You must select a shop.");
		}

        if($_POST['stock']!=null && $_POST['stock']!=" "){
			$stock = htmlentities($_POST['stock']);
            $_SESSION['productAddStock'] = $stock;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the stock quantity.");
		}

        if($_POST['image']!= null && $_POST['image']!=" "){
			$image = htmlentities($_POST['image']);
            $_SESSION['productAddImage'] = $image;
		}else{
			array_push($_SESSION['productErrors'], "You must enter the product image link.");
		}

        if($_POST['discount']!==null && $_POST['discount']!=""){
            $discount=htmlentities($_POST['discount']);
        }
        else{
            $discount = 0;
        }

        if($_POST['minOrder']!==null && $_POST['minOrder']!=""){
            $minOrder=htmlentities($_POST['minOrder']);
        }
        else{
            $minOrder = 1;
        }

        if($_POST['maxOrder']!==null && $_POST['maxOrder']!=""){
            $maxOrder=htmlentities($_POST['maxOrder']);
        }
        else{
            $maxOrder = 20;
        }

        if(sizeof($_SESSION['productErrors'])==0){

            $permissionEmailQuery = "SELECT user_email FROM HAMROMART.users WHERE user_id=$_SESSION[userId]";
            $permissionEmailQueryResult = oci_parse($connection, $permissionEmailQuery);
            oci_execute($permissionEmailQueryResult);

            if($permissionEmailQueryResult){
                $permissionEmail = '';
                
                while($permission = oci_fetch_assoc($permissionEmailQueryResult)){
                    $permissionEmail = $permission['USER_EMAIL'];
                }

                $query= "
                INSERT INTO HAMROMART.product(shop_id,product_name,product_description,min_order,max_order,allergy_information,stock,product_image,discount,product_price, permissions) 
                VALUES('$shop','$name','$description',$minOrder,$maxOrder,'$allergy_info',$stock,'$image',$discount,$price, '$permissionEmail')
                ";
                
                $productQuery=oci_parse($connection,$query);
                oci_execute($productQuery);
                
                if($productQuery){

                    $_SESSION['status']="successfull";
                    header('location:'.$_SESSION['url']);
                    unset($_SESSION['url']);
                    exit();
                    
                }else{
                    echo "error adding product";
                }
            }

            
        }
        else{
            header('Location: ./addProduct.php');
        }

	}
    else{
        header('Location: ./addProduct.php');
    }
}


?>