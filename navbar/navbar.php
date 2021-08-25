<?php
    include '../init.php';

    $cartSize=0;
    if(isset($_SESSION['currentCart'])){
        $cartSize = sizeof($_SESSION['currentCart']);
    }
?>

<nav>

    <div class="nav-container">
        <div class="logo">
            <a href="../landing/index.php">
                <div>
                    <img src="https://i.imgur.com/JtyhTVa.png" alt="">
                </div>
            </a>
        </div>

        <?php
        
            if(isset($_SESSION['userRole'])&&$_SESSION['userRole']=='trader'){
                echo "<ul class='nav-links'>";
                echo "<li><a href='../traderStats/traderStats.php'>My Stats</a></li>";
                echo "<li><a href='../productCRUD/addProduct.php'>Add Product</a></li>";
                echo "<li><a href='../shopCRUD/addShop.php'>Add Shop</a></li>";
                echo "<li><a href='http://localhost:8080/apex/f?p=105:LOGIN_DESKTOP:6296620210470:::::' target='blank'>Database Reports</a></li>";
                echo "<li><a href='../login/logout.php'>Log Out</a></li>";
                echo "</ul>";

                echo "<div class='function-tray'>";
                echo "<svg class='user-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm0 22c-3.123 0-5.914-1.441-7.749-3.69.259-.588.783-.995 1.867-1.246 2.244-.518 4.459-.981 3.393-2.945-3.155-5.82-.899-9.119 2.489-9.119 3.322 0 5.634 3.177 2.489 9.119-1.035 1.952 1.1 2.416 3.393 2.945 1.082.25 1.61.655 1.871 1.241-1.836 2.253-4.628 3.695-7.753 3.695z'/></svg>";
                echo "<a href='../settings/settings.php' class='user-name'>Settings</a>";
                echo "</div>";
            }
            elseif(isset($_SESSION['userRole'])&&$_SESSION['userRole']=='admin'){
                echo "<ul class='nav-links nav-links-full'>";
                echo "<li><a href='../admin/traders.php'>Trader Requests</a></li>";
                echo "<li><a href='../admin/products.php'>All Products</a></li>";
                echo "<li><a href='http://localhost:8080/apex/f?p=105:LOGIN_DESKTOP:6296620210470:::::' target='blank'>Database Reports</a></li>";
                echo "<li><a href='../login/logout.php'>Log Out</a></li>";
                echo "</ul>";
            }
            elseif(isset($_SESSION['userId'])){
                echo "<ul class='nav-links'>";
                echo "<li><a href='../products/products.php'>Products</a></li>";
                echo "<li><a href='../cart/cart.php'>My Cart<sup>$cartSize</sup></a></li>";
                echo "<li><a href='../orderDetails/orderDetails.php'>Order History</a></li>";
                echo "<li><a href='../login/logout.php'>Log Out</a></li>";
                echo "</ul>";

                echo "<div class='function-tray'>";
                echo "<form action='../products/products.php' method='GET'>";
                echo "<svg class='navbar-search-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z'/></svg>";
                echo "<input class='navbar-search-input' type='text' name='product_name'>";
                echo "</form>";
                echo "<svg class='user-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm0 22c-3.123 0-5.914-1.441-7.749-3.69.259-.588.783-.995 1.867-1.246 2.244-.518 4.459-.981 3.393-2.945-3.155-5.82-.899-9.119 2.489-9.119 3.322 0 5.634 3.177 2.489 9.119-1.035 1.952 1.1 2.416 3.393 2.945 1.082.25 1.61.655 1.871 1.241-1.836 2.253-4.628 3.695-7.753 3.695z'/></svg>";
                echo "<a href='../settings/settings.php' class='user-name'>Settings</a>";
                echo "</div>";
            }
            else{
                echo "<ul class='nav-links'>";
                echo "<li><a href='../products/products.php'>Products</a></li>";
                echo "<li><a href='../cart/cart.php'>My Cart<sup>$cartSize</sup></a></li>";
                echo "<li><a href='../login/customerLogin.php'>Log In</a></li>";
                echo "<li><a href='../register/customerRegister.php'>Register</a></li>";
                echo "</ul>";

                echo "<div class='function-tray'>";
                echo "<form action='../products/products.php' method='GET'>";
                echo "<svg class='navbar-search-icon' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M23.809 21.646l-6.205-6.205c1.167-1.605 1.857-3.579 1.857-5.711 0-5.365-4.365-9.73-9.731-9.73-5.365 0-9.73 4.365-9.73 9.73 0 5.366 4.365 9.73 9.73 9.73 2.034 0 3.923-.627 5.487-1.698l6.238 6.238 2.354-2.354zm-20.955-11.916c0-3.792 3.085-6.877 6.877-6.877s6.877 3.085 6.877 6.877-3.085 6.877-6.877 6.877c-3.793 0-6.877-3.085-6.877-6.877z'/></svg>";
                echo "<input class='navbar-search-input' type='text' name='product_name'>";
                echo "</form>";
                echo "</div>";
            }

            echo "
                <span class='hamburger-menu'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M24 6h-24v-4h24v4zm0 4h-24v4h24v-4zm0 8h-24v4h24v-4z'/></svg>
                </span>
            "
        
        ?>

    </div>

</nav>
