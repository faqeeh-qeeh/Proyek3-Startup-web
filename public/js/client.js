// // Custom JavaScript for client area
// $(document).ready(function() {
//     // Enable tooltips
//     $('[data-bs-toggle="tooltip"]').tooltip();
    
//     // Auto-dismiss alerts after 5 seconds
//     setTimeout(function() {
//         $('.alert').alert('close');
//     }, 5000);
    
//     // Handle any client-specific JS here
// });

$(document).ready(function() {
    // Enable tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Theme Toggle Functionality
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;
    
    // Check for saved theme preference or use preferred color scheme
    const savedTheme = localStorage.getItem('theme') || 
                      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    // Apply the saved theme
    if (savedTheme === 'dark') {
        htmlElement.setAttribute('data-bs-theme', 'dark');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    } else {
        htmlElement.setAttribute('data-bs-theme', 'light');
        themeIcon.classList.replace('fa-sun', 'fa-moon');
    }
    
    // Theme toggle click event
    themeToggle.addEventListener('click', function() {
        if (htmlElement.getAttribute('data-bs-theme') === 'dark') {
            htmlElement.setAttribute('data-bs-theme', 'light');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
        } else {
            htmlElement.setAttribute('data-bs-theme', 'dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
        }
        
        // Add animation class
        themeIcon.classList.add('rotate-180');
        setTimeout(() => {
            themeIcon.classList.remove('rotate-180');
        }, 300);
    });
    
    // Listen for system color scheme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                htmlElement.setAttribute('data-bs-theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            } else {
                htmlElement.setAttribute('data-bs-theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            }
        }
    });
    
    // Smooth scrolling for anchor links
    $('a[href*="#"]').on('click', function(e) {
        e.preventDefault();
        
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 80,
            },
            500,
            'linear'
        );
    });
    
    // Initialize any other custom JS components here
});