<?php
/**
 * Helper functions for Taman Kopses Ciseeng website
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect to a specific page
 * @param string $location
 */
function redirect($location) {
    header("Location: $location");
    exit;
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Sanitize user input
 * @param string $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Display flash message
 * @param string $name
 * @param string $message
 * @param string $class
 */
function setFlashMessage($name, $message, $class = "alert alert-success") {
    if (!empty($name) && !empty($message) && !empty($class)) {
        $_SESSION[$name] = $message;
        $_SESSION[$name . '_class'] = $class;
    }
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="' . $_SESSION['message_class'] . '" role="alert">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_class']);
    }
}

/**
 * Format currency
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Check if date is a weekday (Monday-Friday)
 * @param string $date
 * @return bool
 */
function isWeekday($date) {
    $dayOfWeek = date('N', strtotime($date));
    return ($dayOfWeek >= 1 && $dayOfWeek <= 5);
}

/**
 * Generate QR Code for e-ticket
 * @param string $data
 * @return string Base64 encoded QR code image
 */
function generateQRCode($data) {
    // Placeholder for QR code generation
    // In a real implementation, you would use a library like phpqrcode
    return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=";
}

/**
 * Send email
 * @param string $to
 * @param string $subject
 * @param string $message
 * @return bool
 */
function sendEmail($to, $subject, $message) {
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Taman Kopses Ciseeng <noreply@tamankopsesciseeng.com>' . "\r\n";
    
    // Send email
    return mail($to, $subject, $message, $headers);
}

/**
 * Get package price based on date (weekday/weekend)
 * @param array $package
 * @param string $date
 * @return float
 */
function getPackagePrice($package, $date) {
    return isWeekday($date) ? $package['price_weekday'] : $package['price_weekend'];
}

/**
 * Get day type (weekday/weekend) based on date
 * @param string $date
 * @return string
 */
function getDayType($date) {
    return isWeekday($date) ? 'weekday' : 'weekend';
}
?>
