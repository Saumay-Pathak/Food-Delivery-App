// JavaScript for sidebar toggle and content shift
document.querySelector('.sidebar-toggle').addEventListener('click', function() {
    var sidebar = document.querySelector('.sidebar');
    var mainContentWrapper = document.querySelector('.main-content-wrapper');

    // Toggle the sidebar visibility
    sidebar.classList.toggle('open');

    // Toggle the main content shift
    mainContentWrapper.classList.toggle('shifted');
});

// JavaScript to hide the button when scrolling down, show it when scrolling up
let lastScrollTop = 0;
const sidebarButton = document.querySelector('.sidebar-toggle');

window.addEventListener('scroll', function() {
    let scrollTop = window.scrollY || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
        // Scrolling down
        sidebarButton.classList.add('hidden');
        sidebarButton.classList.remove('visible');
    } else {
        // Scrolling up
        sidebarButton.classList.add('visible');
        sidebarButton.classList.remove('hidden');
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Prevent negative values
});
