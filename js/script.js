var sideBarisOpen = true; // Start with the sidebar being open

// Select elements
const nav = document.getElementById('nav');
const dashboard_sidebar = document.getElementById('dashboard_sidebar');
const dashboard_content_container = document.getElementById('dashboard_content_container');
const dashboard_logo = document.getElementById('dashboard_logo');
const User_image = document.getElementById('User_image');
const user_name = document.getElementById('user_name');

// Handle the nav button click event
nav.addEventListener('click', (event) => {
    event.preventDefault();  // Prevent the default behavior of the nav button

    // Toggle the sidebar state
    if (sideBarisOpen) {
        // Collapse the sidebar
        dashboard_sidebar.style.width = '10%';  // Sidebar width
        dashboard_content_container.style.marginLeft = '10%'; // Adjust content margin
        dashboard_content_container.style.width = '90%'; // Adjust content width

        // Adjust the logo and user image sizes when collapsed
        dashboard_logo.style.fontSize = '25px';
        User_image.style.width = '30px';
        User_image.style.height = '30px';
        user_name.style.fontSize = '12px';

        // Hide menu text in collapsed state
        const menuTextElements = document.getElementsByClassName('menuText');
        for (let i = 0; i < menuTextElements.length; i++) {
            menuTextElements[i].style.display = 'none';  // Hide text
        }

        // Change the state of sidebar (collapsed)
        sideBarisOpen = false;
    } else {
        // Expand the sidebar
        dashboard_sidebar.style.width = '20%';  // Sidebar width
        dashboard_content_container.style.marginLeft = '20%'; // Adjust content margin
        dashboard_content_container.style.width = '80%'; // Adjust content width

        // Adjust the logo and user image sizes when expanded
        dashboard_logo.style.fontSize = '50px';
        User_image.style.width = '50px';
        User_image.style.height = '50px';
        user_name.style.fontSize = '22px';

        // Show menu text in expanded state
        const menuTextElements = document.getElementsByClassName('menuText');
        for (let i = 0; i < menuTextElements.length; i++) {
            menuTextElements[i].style.display = 'inline-block';  // Show text
        }

        // Change the state of sidebar (expanded)
        sideBarisOpen = true;
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        let clickedE1 = e.target;

        // Find the closest parent li with the class 'limainMenu'
        let parentLi = clickedE1.closest('.limainMenu');
        if (parentLi && parentLi.classList.contains('triggerMenu')) {
            let targetMenu = parentLi.dataset.submenu; // Get the target submenu's id
            if (targetMenu) {
                let submenu = document.getElementById(targetMenu);
                let icon = parentLi.querySelector('.MainMenuIcon'); // Target the icon

                // Toggle visibility of the submenu
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none'; // Hide the submenu
                    if (icon) icon.classList.replace('fa-angle-down', 'fa-angle-left'); // Change icon to left
                } else {
                    submenu.style.display = 'block'; // Show the submenu
                    if (icon) icon.classList.replace('fa-angle-left', 'fa-angle-down'); // Change icon to down
                }
            }
        }
    });
});

 // Highlight the active menu based on the current URL
 const currentPage = window.location.pathname.split('/').pop(); // Get the current page file name

 const allMenuLinks = document.querySelectorAll('.dashboard_menu_lists a');
 allMenuLinks.forEach(link => {
     const menuLink = link.getAttribute('href');
     if (menuLink.includes(currentPage)) {
         const parentLi = link.closest('li.limainMenu');
         if (parentLi) {
             parentLi.classList.add('active'); // Add the 'active' class for the current menu item
         }
     }
 });
document.addEventListener('DOMContentLoaded', function () {
    const passwordField = document.querySelector('#password');
    const togglePassword = document.querySelector('#togglePassword');

    if (passwordField && togglePassword) {
        togglePassword.addEventListener('click', function () {
            // Toggle the input type between 'password' and 'text'
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle the icon class between 'fa-eye' and 'fa-eye-slash'
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});
