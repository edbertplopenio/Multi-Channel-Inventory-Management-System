// Select all menu items with submenus
const menuItemsWithSubmenus = document.querySelectorAll('.menu > ul > li');

// Function to remove active class from all menu items and close submenus
function removeActiveClasses() {
    menuItemsWithSubmenus.forEach(item => {
        item.classList.remove('active');
        const submenu = item.querySelector('.sub-menu');
        if (submenu) {
            submenu.style.display = 'none'; // Close submenu
        }
    });

    // Remove active class from all submenu items
    const submenuItems = document.querySelectorAll('.sub-menu li a');
    submenuItems.forEach(submenuItem => submenuItem.classList.remove('active'));
}

// Add click event listener to each menu item
menuItemsWithSubmenus.forEach(item => {
    const submenu = item.querySelector('.sub-menu');
    const arrow = item.querySelector('.arrow');

    item.addEventListener('click', (event) => {
        event.stopPropagation(); // Prevent event from bubbling up
        const isSubmenuVisible = submenu && submenu.style.display === 'block';
        
        // Remove active classes from other items
        removeActiveClasses();

        if (submenu) {
            if (isSubmenuVisible) {
                submenu.style.display = 'none'; // Hide submenu
                item.classList.remove('active'); // Remove active class
            } else {
                submenu.style.display = 'block'; // Show submenu
                item.classList.add('active'); // Add active class
            }
        } else {
            item.classList.add('active'); // Add active class for items without submenus
        }
    });
});

// Add click event listener to submenu items to highlight the selected item
const submenuItems = document.querySelectorAll('.sub-menu li a');

submenuItems.forEach(submenuItem => {
    submenuItem.addEventListener('click', (event) => {
        event.stopPropagation(); // Prevent event from bubbling up
        // Remove active class from all submenu items
        submenuItems.forEach(item => item.classList.remove('active'));
        // Add active class to the clicked submenu item
        submenuItem.classList.add('active');
    });
});
