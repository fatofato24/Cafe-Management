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

         <!-- Product Management -->
         <li class="limainMenu triggerMenu" data-submenu="user">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-user-plus"></i>
        <span class="menuText">User Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="user">
        <a href="./users-view.php" class="subMenuLink"><i class="fa fa-circle"></i> View Users</a>
        <a href="./users-add.php" class="subMenuLink"><i class="fa fa-circle"></i> Add Users</a>
    </ul>
</li>

<li class="limainMenu triggerMenu" data-submenu="product">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-cogs"></i>
        <span class="menuText">Product Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="product">
        <a href="./product-view.php" class="subMenuLink"><i class="fa fa-circle"></i> View Products</a>
        <a href="./product-add.php" class="subMenuLink"><i class="fa fa-circle"></i> Add Products</a>
    </ul>
</li>

<li class="limainMenu triggerMenu" data-submenu="supplier">
    <a href="javascript:void(0);" class="limainMenu_link">
        <i class="fa fa-truck"></i>
        <span class="menuText">Supplier Management</span>
        <i class="fa fa-angle-down MainMenuIcon"></i>
    </a>
    <ul class="subMenus" id="supplier">
        <a href="#" class="subMenuLink"><i class="fa fa-circle"></i> View Suppliers</a>
        <a href="#" class="subMenuLink"><i class="fa fa-circle"></i> Add Suppliers</a>
    </ul>
</li>

    </ul>
</div>
</div>

