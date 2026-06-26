<?php
/**
 * Database Seeders - Create default users
 * Gunakan script ini untuk add/create default users ke database
 * 
 * Run di browser: http://localhost/tirtasanita/database/seeders.php
 */

require_once dirname(__FILE__) . '/../config/database.php';

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   DATABASE SEEDER - CREATE DEFAULT USERS                      ║\n";
echo "║   Tirta Sanita Outbound                                        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Get database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "❌ Database connection failed!\n";
    exit(1);
}

// Define default users
$default_users = [
    [
        'name' => 'Admin',
        'whatsapp' => '0812345678910',
        'email' => 'admin@tirtasanita.com',
        'password' => 'admin123',
        'role' => 'admin'
    ],
    [
        'name' => 'Kasir',
        'whatsapp' => '08123456789',
        'email' => 'kasir@tirtasanita.com',
        'password' => 'kasir123',
        'role' => 'cashier'
    ],
    [
        'name' => 'Naya',
        'whatsapp' => '081234567891011',
        'email' => 'naya@tirtasanita.com',
        'password' => 'naya123',
        'role' => 'user'
    ]
];

echo "📋 DEFAULT USERS TO CREATE:\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

foreach ($default_users as $index => $user) {
    echo ($index + 1) . ". {$user['name']}\n";
    echo "   WhatsApp: {$user['whatsapp']}\n";
    echo "   Email:    {$user['email']}\n";
    echo "   Password: {$user['password']}\n";
    echo "   Role:     {$user['role']}\n\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "🔄 PROCESSING...\n\n";

// Delete existing users first
echo "1️⃣  Clearing existing users...\n";
try {
    $delete_sql = "DELETE FROM users";
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->execute();
    echo "   ✓ Existing users cleared\n\n";
} catch (Exception $e) {
    echo "   ⚠️  Could not clear existing users (table might be empty)\n\n";
}

// Reset AUTO_INCREMENT
echo "2️⃣  Resetting AUTO_INCREMENT...\n";
try {
    $reset_sql = "ALTER TABLE users AUTO_INCREMENT = 1";
    $reset_stmt = $db->prepare($reset_sql);
    $reset_stmt->execute();
    echo "   ✓ AUTO_INCREMENT reset\n\n";
} catch (Exception $e) {
    echo "   ⚠️  Could not reset AUTO_INCREMENT\n\n";
}

// Insert default users
echo "3️⃣  Creating default users...\n\n";

$insert_sql = "INSERT INTO users (name, whatsapp, email, password, role) VALUES (:name, :whatsapp, :email, :password, :role)";
$success_count = 0;
$error_count = 0;

foreach ($default_users as $user) {
    try {
        $insert_stmt = $db->prepare($insert_sql);
        
        $insert_stmt->bindParam(':name', $user['name']);
        $insert_stmt->bindParam(':whatsapp', $user['whatsapp']);
        $insert_stmt->bindParam(':email', $user['email']);
        $insert_stmt->bindParam(':password', $user['password']);
        $insert_stmt->bindParam(':role', $user['role']);
        
        if ($insert_stmt->execute()) {
            echo "   ✅ {$user['role']}: {$user['name']}\n";
            echo "      WhatsApp: {$user['whatsapp']}\n";
            echo "      Password: {$user['password']}\n\n";
            $success_count++;
        } else {
            echo "   ❌ Failed to create {$user['role']}: {$user['name']}\n";
            echo "      Error: " . $insert_stmt->errorInfo()[2] . "\n\n";
            $error_count++;
        }
    } catch (Exception $e) {
        echo "   ❌ Exception creating {$user['role']}: {$user['name']}\n";
        echo "      Error: " . $e->getMessage() . "\n\n";
        $error_count++;
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 SEEDER RESULT:\n\n";
echo "✅ Successfully created: {$success_count} users\n";
echo "❌ Failed:               {$error_count} users\n\n";

// Final verification
echo "🔍 VERIFICATION:\n";
echo "───────────────────────────────────────────────────────────────\n\n";

try {
    $verify_sql = "SELECT id, name, whatsapp, email, password, role FROM users ORDER BY id";
    $verify_stmt = $db->prepare($verify_sql);
    $verify_stmt->execute();
    
    if ($verify_stmt->rowCount() > 0) {
        echo "ID | Role     | Name      | WhatsApp      | Password   | Email\n";
        echo "─────────────────────────────────────────────────────────────\n";
        
        while ($row = $verify_stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = str_pad($row['id'], 2);
            $role = str_pad($row['role'], 8);
            $name = str_pad($row['name'], 9);
            $whatsapp = str_pad($row['whatsapp'], 13);
            $password = str_pad($row['password'], 10);
            
            echo "{$id} | {$role} | {$name} | {$whatsapp} | {$password} | {$row['email']}\n";
        }
        
        echo "\n✅ Users successfully created in database!\n\n";
        
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "🚀 LOGIN CREDENTIALS:\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";
        
        echo "👤 ADMIN:\n";
        echo "   URL:      http://localhost/tirtasanita/admin\n";
        echo "   WhatsApp: 0812345678910\n";
        echo "   Password: admin123\n\n";
        
        echo "👥 KASIR:\n";
        echo "   URL:      http://localhost/tirtasanita/admin\n";
        echo "   WhatsApp: 08123456789\n";
        echo "   Password: kasir123\n\n";
        
        echo "═══════════════════════════════════════════════════════════════\n";
        
    } else {
        echo "⚠️  No users found in database!\n";
    }
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
}

echo "\n✨ SEEDING COMPLETE!\n\n";
?>
