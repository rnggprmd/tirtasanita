<?php
/**
 * Midtrans Webhook Handler
 * Handle real-time notifications dari Midtrans
 * 
 * Setup di Midtrans Dashboard:
 * Settings > Webhooks > Tambahkan URL ini:
 * https://yourdomain.com/tintasanita/webhook/midtrans.php
 */

require_once '../config/database.php';
require_once '../config/midtrans.php';

// Get webhook notification from Midtrans
$notification = json_decode(file_get_contents("php://input"), true);

// Log notification untuk debugging
error_log('Midtrans Webhook: ' . json_encode($notification));

// Validate notification (simple check)
if (!isset($notification['order_id'])) {
    http_response_code(400);
    exit('Invalid notification');
}

// Get order ID dan status
$order_id = $notification['order_id'];
$transaction_status = $notification['transaction_status'] ?? null;
$fraud_status = $notification['fraud_status'] ?? null;

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get payment record
$sql = "SELECT id, reservation_id, status FROM payments WHERE transaction_id = :order_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':order_id', $order_id);
$stmt->execute();
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    error_log("Midtrans: Payment not found for order_id: $order_id");
    http_response_code(404);
    exit('Payment not found');
}

$payment_id = $payment['id'];
$reservation_id = $payment['reservation_id'];
$payment_status = $payment['status'];

// Process berdasarkan transaction status
$update_status = false;
$new_payment_status = $payment_status;
$new_reservation_status = 'pending';

if ($transaction_status == 'capture') {
    // Credit card captured
    if ($fraud_status == 'accept') {
        $update_status = true;
        $new_payment_status = 'completed';
        $new_reservation_status = 'confirmed';
    }
} else if ($transaction_status == 'settlement') {
    // Payment berhasil dan settlement
    $update_status = true;
    $new_payment_status = 'completed';
    $new_reservation_status = 'confirmed';
} else if ($transaction_status == 'pending') {
    // Payment pending
    $update_status = true;
    $new_payment_status = 'pending';
    $new_reservation_status = 'pending';
} else if ($transaction_status == 'deny') {
    // Payment denied
    $update_status = true;
    $new_payment_status = 'failed';
    $new_reservation_status = 'cancelled';
} else if ($transaction_status == 'expire') {
    // Payment expired
    $update_status = true;
    $new_payment_status = 'failed';
    $new_reservation_status = 'cancelled';
} else if ($transaction_status == 'cancel') {
    // Payment cancelled
    $update_status = true;
    $new_payment_status = 'failed';
    $new_reservation_status = 'cancelled';
}

// Update database if status changed
if ($update_status && $new_payment_status != $payment_status) {
    try {
        // Update payment status
        $sql = "UPDATE payments SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':status', $new_payment_status);
        $stmt->bindParam(':id', $payment_id);
        $stmt->execute();

        // Update reservation status
        $sql = "UPDATE reservations SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':status', $new_reservation_status);
        $stmt->bindParam(':id', $reservation_id);
        $stmt->execute();

        // Log update
        error_log("Midtrans: Updated order_id=$order_id, payment_status=$new_payment_status, reservation_status=$new_reservation_status");

        // Bisa tambahkan notifikasi email di sini
        if ($new_payment_status == 'completed') {
            sendPaymentConfirmationEmail($db, $reservation_id);
        }

        http_response_code(200);
        echo "Webhook processed successfully";
    } catch (Exception $e) {
        error_log("Midtrans Webhook Error: " . $e->getMessage());
        http_response_code(500);
        echo "Error processing webhook";
    }
} else {
    http_response_code(200);
    echo "No status change needed";
}

/**
 * Send payment confirmation email
 * @param PDO $db
 * @param int $reservation_id
 */
function sendPaymentConfirmationEmail($db, $reservation_id) {
    try {
        // Get reservation details
        $sql = "SELECT r.*, u.email, u.name, u.whatsapp, p.name as package_name 
                FROM reservations r 
                JOIN users u ON r.user_id = u.id 
                JOIN packages p ON r.package_id = p.id 
                WHERE r.id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $reservation_id);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            return false;
        }

        // Prepare email
        $to = $reservation['email'];
        $subject = "Pembayaran Berhasil - Reservasi Tirta Sanita Outbound";
        
        $message = "
        <html><body>
        <h2>Pembayaran Berhasil Dikonfirmasi!</h2>
        <p>Halo {$reservation['name']},</p>
        <p>Terima kasih telah melakukan pembayaran untuk reservasi Anda di Tirta Sanita Outbound.</p>
        
        <h3>Detail Reservasi:</h3>
        <ul>
            <li>Paket: {$reservation['package_name']}</li>
            <li>Tanggal Kunjungan: " . date('d/m/Y', strtotime($reservation['visit_date'])) . "</li>
            <li>Jumlah Peserta: {$reservation['num_visitors']} orang</li>
            <li>Total Pembayaran: Rp " . number_format($reservation['total_price'], 0, ',', '.') . "</li>
        </ul>
        
        <p>Tim kami akan menghubungi Anda di WhatsApp ({$reservation['whatsapp']}) untuk konfirmasi lebih lanjut.</p>
        
        <p>Jika ada pertanyaan, silakan hubungi kami di:</p>
        <ul>
            <li>WhatsApp: 0858-1077-1107</li>
            <li>Email: info@tirtasanita.com</li>
        </ul>
        
        <p>Terima kasih,<br>Tim Tirta Sanita Outbound</p>
        </body></html>
        ";

        // Email headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Tirta Sanita Outbound <noreply@tirtasanita.com>\r\n";

        // Send email
        mail($to, $subject, $message, $headers);

        return true;
    } catch (Exception $e) {
        error_log("Error sending confirmation email: " . $e->getMessage());
        return false;
    }
}

?>
