/**
 * Sidebar Overlay and Mobile Responsiveness Handler
 * Used by Admin & Cashier panels
 */
$(document).ready(function() {
    // Sync overlay state when sidebar toggle is clicked
    $(document).on('click', '#sidebar-toggle', function() {
        if ($(window).width() < 992) {
            setTimeout(function() {
                if ($('.sidebar').hasClass('active')) {
                    $('.sidebar-overlay').addClass('active');
                } else {
                    $('.sidebar-overlay').removeClass('active');
                }
            }, 10);
        }
    });

    // Close sidebar when clicking overlay
    $(document).on('click', '.sidebar-overlay', function() {
        $('.sidebar').removeClass('active');
        $('.sidebar-overlay').removeClass('active');
    });

    // Handle window resize
    $(window).resize(function() {
        if ($(window).width() >= 992) {
            $('.sidebar-overlay').removeClass('active');
        }
    });
});
