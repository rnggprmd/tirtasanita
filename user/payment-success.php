<?php
require_once '../config/database.php';
require_once '../config/midtrans.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect("login.php");
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    redirect("dashboard.php");
}

$database = new Database();
$db = $database->getConnection();

// SECURITY: Check if order_id actually exists in our database
// Join dengan reservations untuk verify user ownership
$sql = "SELECT p.id, p.reservation_id, p.status, p.amount 
        FROM payments p
        JOIN reservations r ON p.reservation_id = r.id
        WHERE p.transaction_id = :order_id AND r.user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();

$existing_payment = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika payment record tidak ada atau bukan milik user, reject
if (!$existing_payment) {
    setFlashMessage('message', 'Pembayaran tidak ditemukan atau tidak valid.', 'alert alert-danger');
    redirect("dashboard.php");
}

$reservation_id = $existing_payment['reservation_id'];
$payment_id = $existing_payment['id'];

// Get Midtrans transaction status for verification
$status = MidtransConfig::getTransactionStatus($order_id);

// Update berdasarkan status dari Midtrans (server-side verification)
if ($status) {
    switch ($status->transaction_status) {
        case 'capture':
        case 'settlement':
            // Pembayaran berhasil
            if ($existing_payment['status'] != 'completed') {
                // Update reservation status to confirmed
                $sql = "UPDATE reservations SET status = 'confirmed' WHERE id = :id AND user_id = :user_id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $reservation_id);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();

                // Update payment status
                $sql = "UPDATE payments SET status = 'completed', updated_at = NOW() WHERE id = :id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $payment_id);
                $stmt->execute();
            }
            $payment_confirmed = true;
            break;
        
        case 'pending':
        case 'deny':
        case 'cancel':
        case 'expire':
        default:
            // Pembayaran gagal atau pending
            $payment_confirmed = false;
            break;
    }
} else {
    // Jika tidak bisa verify via Midtrans, check database status
    // Ini fallback jika webhook sudah memproses pembayaran lebih dulu
    $payment_confirmed = ($existing_payment['status'] == 'completed');
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Pembayaran Berhasil - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="../img/favicon.ico" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4dc387;
            --primary-dark: #3da876;
        }

        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 20px;
        }

        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 50px;
            text-align: center;
            max-width: 500px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.6s ease;
        }

        .success-icon i {
            color: white;
            font-size: 40px;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .success-card h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .success-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-details {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: var(--primary-color);
            font-weight: 700;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <h2>Pembayaran Berhasil!</h2>
            <p>Terima kasih telah melakukan pembayaran. Reservasi Anda telah dikonfirmasi dan kami akan mengirimkan detail lebih lanjut melalui WhatsApp.</p>

            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">No. Pesanan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">✓ Pembayaran Dikonfirmasi</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Waktu:</span>
                    <span class="detail-value"><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>

            <p style="color: #999; font-size: 14px;">Cek email Anda untuk detail reservasi dan invoice pembayaran.</p>

            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-primary">Lihat Reservasi</a>
                <a href="../index.php" class="btn btn-secondary">Kembali ke Home</a>
            </div>
        </div>
    </div>
</body>

</html>
