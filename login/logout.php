<?php

    include '../init.php';
    unset($_SESSION['userId']);
    unset($_SESSION['userRole']);
    unset($_SESSION['currentCart']);

    header('Location: ../login/customerLogin.php');


?>