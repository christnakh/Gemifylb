document.addEventListener('DOMContentLoaded', function() {
    const toggleMenu = document.querySelector('.toggle-menu');
    const navUl = document.querySelector('header nav ul');

    toggleMenu.addEventListener('click', function() {
        navUl.classList.toggle('active');
    });
});
