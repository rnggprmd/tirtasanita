<?php
// Include functions file
require_once '../includes/functions.php';

// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
redirect("index.php");
exit;
?>
