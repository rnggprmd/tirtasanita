<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's a delete action
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            
            // Get current image filename if it's a QRIS payment method
            $sql = "SELECT type, qr_image FROM payment_methods WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $method = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Delete the image file if it exists
                if ($method['type'] == 'qris' && !empty($method['qr_image'])) {
                    $image_path = '../uploads/payments/qris/' . $method['qr_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                // Delete the payment method
                $sql = "DELETE FROM payment_methods WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    setFlashMessage('message', 'Metode pembayaran berhasil dihapus.', 'alert alert-success');
                } else {
                    setFlashMessage('message', 'Gagal menghapus metode pembayaran.', 'alert alert-danger');
                }
            } else {
                setFlashMessage('message', 'Metode pembayaran tidak ditemukan.', 'alert alert-danger');
            }
        } else {
            setFlashMessage('message', 'ID metode pembayaran tidak valid.', 'alert alert-danger');
        }
        
        redirect("settings.php");
        exit;
    }
    
    // Add or Edit payment method
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $type = $_POST['type'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Set account info based on payment type
    $account_info = null;
    if ($type == 'bank_transfer' && isset($_POST['account_info'])) {
        $account_info = trim($_POST['account_info']);
    } elseif ($type == 'ewallet' && isset($_POST['ewallet_account'])) {
        $account_info = trim($_POST['ewallet_account']);
    }
    
    // Handle file upload for QRIS
    $qr_image = null;
    $upload_success = true;
    
    if ($type == 'qris' && isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['qr_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $new_filename = 'qris_' . time() . '_' . uniqid() . '.' . $filetype;
            
            // Create directory if it doesn't exist
            $upload_dir = '../uploads/payments/qris/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            
            // Upload file
            if (move_uploaded_file($_FILES['qr_image']['tmp_name'], $upload_path)) {
                $qr_image = $new_filename;
            } else {
                $upload_success = false;
                setFlashMessage('message', 'Gagal mengupload gambar QRIS.', 'alert alert-danger');
            }
        } else {
            $upload_success = false;
            setFlashMessage('message', 'Format file tidak didukung. Silakan upload file JPG, JPEG, atau PNG.', 'alert alert-danger');
        }
    }
    
    // If upload failed, redirect back to settings page
    if (!$upload_success) {
        redirect("settings.php");
        exit;
    }
    
    // Check if it's an edit (update) or add (insert) operation
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing payment method
        $id = $_POST['id'];
        
        // Get current data to check if we need to delete old QRIS image
        if ($type == 'qris' && $qr_image) {
            $sql = "SELECT qr_image FROM payment_methods WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $old_method = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Delete old image if it exists
                if (!empty($old_method['qr_image'])) {
                    $old_image_path = '../uploads/payments/qris/' . $old_method['qr_image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
            }
        }
        
        // Prepare SQL based on whether we have a new QR image
        if ($type == 'qris' && $qr_image) {
            $sql = "UPDATE payment_methods SET 
                    name = :name, 
                    description = :description, 
                    type = :type, 
                    account_info = :account_info, 
                    qr_image = :qr_image, 
                    is_active = :is_active 
                    WHERE id = :id";
        } else {
            $sql = "UPDATE payment_methods SET 
                    name = :name, 
                    description = :description, 
                    type = :type, 
                    account_info = :account_info, 
                    is_active = :is_active 
                    WHERE id = :id";
        }
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':account_info', $account_info);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':id', $id);
        
        if ($type == 'qris' && $qr_image) {
            $stmt->bindParam(':qr_image', $qr_image);
        }
        
        if ($stmt->execute()) {
            setFlashMessage('message', 'Metode pembayaran berhasil diperbarui.', 'alert alert-success');
        } else {
            setFlashMessage('message', 'Gagal memperbarui metode pembayaran.', 'alert alert-danger');
        }
    } else {
        // Insert new payment method
        $sql = "INSERT INTO payment_methods (name, description, type, account_info, qr_image, is_active) 
                VALUES (:name, :description, :type, :account_info, :qr_image, :is_active)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':account_info', $account_info);
        $stmt->bindParam(':qr_image', $qr_image);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            setFlashMessage('message', 'Metode pembayaran berhasil ditambahkan.', 'alert alert-success');
        } else {
            setFlashMessage('message', 'Gagal menambahkan metode pembayaran.', 'alert alert-danger');
        }
    }
    
    redirect("settings.php");
    exit;
}

// If accessed directly without POST data, redirect to settings page
redirect("settings.php");
?>
