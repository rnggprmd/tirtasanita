<?php
/**
 * Admin Credentials Verification & Fix Script
 * Gunakan script ini untuk memverifikasi dan memperbaiki admin credentials
 */

require_once 'config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("❌ Database connection failed!");
}

echo "═══════════════════════════════════════════════════════════\n";
echo "  ADMIN CREDENTIALS VERIFICATION SCRIPT\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Step 1: Check current admin data
echo "📋 Checking current admin data in database...\n\n";

$sql = "SELECT id, name, whatsapp, password, role FROM users WHERE role = 'admin' LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Current Admin Data:\n";
    echo "  ID:        {$row['id']}\n";
    echo "  Name:      {$row['name']}\n";
    echo "  WhatsApp:  {$row['whatsapp']}\n";
    echo "  Password:  {$row['password']}\n";
    echo "  Role:      {$row['role']}\n\n";
    
    // Step 2: Verify credentials
    $expected_whatsapp = '0812345678910';
    $expected_password = 'admin123';
    
    echo "✓ Expected Credentials:\n";
    echo "  WhatsApp:  {$expected_whatsapp}\n";
    echo "  Password:  {$expected_password}\n\n";
    
    // Step 3: Check if credentials match
    $whatsapp_match = ($row['whatsapp'] === $expected_whatsapp);
    $password_match = ($row['password'] === $expected_password);
    
    if ($whatsapp_match && $password_match) {
        echo "✅ ✅ ✅ CREDENTIALS ARE CORRECT!\n";
        echo "   You can login with:\n";
        echo "   WhatsApp: {$row['whatsapp']}\n";
        echo "   Password: {$row['password']}\n\n";
    } else {
        echo "⚠️  CREDENTIALS MISMATCH!\n\n";
        
        if (!$whatsapp_match) {
            echo "❌ WhatsApp mismatch:\n";
            echo "   Current:  {$row['whatsapp']}\n";
            echo "   Expected: {$expected_whatsapp}\n\n";
        }
        
        if (!$password_match) {
            echo "❌ Password mismatch:\n";
            echo "   Current:  {$row['password']}\n";
            echo "   Expected: {$expected_password}\n\n";
        }
        
        // Step 4: Offer to fix
        echo "🔧 Attempting to fix credentials...\n\n";
        
        $update_sql = "UPDATE users SET whatsapp = :whatsapp, password = :password WHERE role = 'admin' LIMIT 1";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->bindParam(':whatsapp', $expected_whatsapp);
        $update_stmt->bindParam(':password', $expected_password);
        
        if ($update_stmt->execute()) {
            echo "✅ UPDATE SUCCESSFUL!\n\n";
            echo "Your new credentials are:\n";
            echo "  WhatsApp: {$expected_whatsapp}\n";
            echo "  Password: {$expected_password}\n\n";
            echo "Try login again with these credentials.\n";
        } else {
            echo "❌ UPDATE FAILED!\n";
            echo "Error: " . $update_stmt->errorInfo()[2] . "\n";
        }
    }
} else {
    echo "❌ No admin user found in database!\n";
    echo "Please import database/tirtasanita_db.sql first.\n";
}

echo "\n═══════════════════════════════════════════════════════════\n";
echo "  NEXT STEPS:\n";
echo "  1. Access: http://localhost/tirtasanita/admin\n";
echo "  2. WhatsApp: 0812345678910\n";
echo "  3. Password: admin123\n";
echo "═══════════════════════════════════════════════════════════\n";
?>
