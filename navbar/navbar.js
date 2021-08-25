const navbarSearchInput = document.querySelector('.navbar-search-input') || '';
const navbarSearchIcon = document.querySelector('.navbar-search-icon') || '';

navbarSearchIcon &&
    navbarSearchIcon.addEventListener('click', () => {
        navbarSearchInput.classList.toggle('search-input-expanded');
    });

const hamburgerIcon = document.querySelector('.hamburger-menu');
hamburgerIcon.addEventListener('click', () => {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('expanded-nav-link');
});
