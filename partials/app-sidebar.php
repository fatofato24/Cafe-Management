<?php
$user = $_SESSION['user'];
?>


<div class="dashboard_sidebar" id="dashboard_sidebar">
    <h3 class="dashboard_logo" id="dashboard_logo">CIMS</h3>
    <div class="dashboard_sidebar_user" id="dashboard_sidebar_user">
        <img src="images/user.jpg" alt="User image." id="User_image"/>
        <span id="user_name">Sana</span>
    </div>
    
    <!-- Menu Section -->
    <div class="dashboard_sidebar_menus">
    <ul class="dashboard_menu_lists">
        <li class="limainMenu">
            <a href="./dashboard.php"><i class="fa fa-dashboard"></i> <span class="menuText">Dashboard</span></a>
        </li>
        
        <!-- User Management -->
<li class="limainMenu triggerMenu" data-submenu="user">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-user-plus"></i>
        <span class="menuText submenuText" data-full-text="User Management">User Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="user">
        <a href="./users-view.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="View" data-full-text="View Users">View Users</span>
        </a>
        <a href="./users-add.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="Add" data-full-text="Add Users">Add Users</span>
        </a>
    </ul>
</li>

<!-- Product Management -->
<li class="limainMenu triggerMenu" data-submenu="product">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-cogs"></i>
        <span class="menuText submenuText"  data-full-text="Product Management">Product Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="product">
        <a href="./product-view.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="View" data-full-text="View Products">View Products</span>
        </a>
        <a href="./product-add.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="Add" data-full-text="Add Products">Add Products</span>
        </a>
    </ul>
</li>

<!-- Supplier Management -->
<li class="limainMenu triggerMenu" data-submenu="supplier">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-truck"></i>
        <span class="menuText submenuText"  data-full-text="Supplier Management">Supplier Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="supplier">
        <a href="./supplier-view.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="View" data-full-text="View Suppliers">View Suppliers</span>
        </a>
        <a href="./supplier-add.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="Add" data-full-text="Add Suppliers">Add Suppliers</span>
        </a>
    </ul>
</li>   
        <li class="limainMenu triggerMenu" data-submenu="order">
    <a href="javascript:void(0);" class="limainMenu_link">
    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <span class="menuText submenuText"  data-full-text="Order Management">Orders</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="order">
        
        <a href="./product-order.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="Add" data-full-text="Order Products">Order Products</span>
        </a>
        <a href="./order-view.php" class="subMenuLink">
            <i class="fa fa-circle"></i>
            <span class="submenuText" data-short-text="View" data-full-text="View Order">View Order</span>
        </a>
    </ul>
</li>
<li class="limainMenu">
            <a href="./report.php"><i class="fa fa-file"></i> <span class="menuText">Reports</span></a>
        </li>
    </ul>
</div>
</div>

