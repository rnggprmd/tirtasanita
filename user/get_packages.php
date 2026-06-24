<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if request is AJAX
if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    
    // Get packages for the selected category
    $database = new Database();
    $db = $database->getConnection();
    
    $sql = "SELECT id, name, price_weekday, price_weekend FROM packages WHERE category_id = :category_id AND is_active = 1 ORDER BY name";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return packages as JSON
    header('Content-Type: application/json');
    echo json_encode($packages);
} else {
    // Return empty array if no category_id is provided
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>
