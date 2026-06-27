<?php
require_once '../config/database.php';
require_once '../config/midtrans.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu untuk melakukan reservasi.', 'alert alert-danger');
    redirect("login.php");
}

// Initialize variables
$database = new Database();
$db = $database->getConnection();

// Get user data
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_whatsapp = $_SESSION['user_whatsapp'];

// Get reservation ID from URL parameter
$reservation_id = $_GET['id'] ?? null;

if (!$reservation_id) {
    setFlashMessage('message', 'Reservation tidak ditemukan.', 'alert alert-danger');
    redirect("dashboard.php");
}

// Get reservation details
$sql = "SELECT r.*, p.name as package_name, p.price_weekday, p.price_weekend 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'pending'";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservation tidak ditemukan atau sudah diproses.', 'alert alert-danger');
    redirect("dashboard.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Get reservation data
$package_id = $reservation['package_id'];
$package_name = $reservation['package_name'];
$visit_date = $reservation['visit_date'];
$num_visitors = $reservation['num_visitors'];
$is_weekday = $reservation['is_weekday'];
$total_price = $reservation['total_price'];

// Calculate price per person
$price_per_person = $total_price / $num_visitors;

// Create order ID
$order_id = 'TIRT-' . $user_id . '-' . time();

// Prepare transaction data for Midtrans
$transaction_data = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => (int)$total_price
    ],
    'customer_details' => [
        'first_name' => $user_name,
        'email' => $_SESSION['user_email'] ?? 'user@tirtasanita.com',
        'phone' => $user_whatsapp
    ],
    'item_details' => [
        [
            'id' => 'pkg-' . $package_id,
            'price' => (int)$price_per_person,
            'quantity' => (int)$num_visitors,
            'name' => $package_name . ' (' . $num_visitors . ' orang)'
        ]
    ],
    'callbacks' => [
        'finish' => MidtransConfig::getBaseUrl() . '/user/payment-success.php',
        'error' => MidtransConfig::getBaseUrl() . '/user/payment-error.php',
        'pending' => MidtransConfig::getBaseUrl() . '/user/payment-pending.php'
    ]
];

// Get Snap Token from Midtrans
$snap_token = MidtransConfig::createSnapToken($transaction_data);

if (!$snap_token) {
    setFlashMessage('message', 'Gagal membuat token pembayaran. Silakan coba lagi.', 'alert alert-danger');
    redirect("dashboard.php");
}

// Save payment record (or update if already exists)
$sql = "SELECT id FROM payments WHERE reservation_id = :reservation_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':reservation_id', $reservation_id);
$stmt->execute();

$payment_method_id = 7; // 7 adalah Midtrans SNAP dari payment_methods table

if ($stmt->rowCount() > 0) {
    // Payment already exists, update it
    $existing_payment = $stmt->fetch(PDO::FETCH_ASSOC);
    $payment_id = $existing_payment['id'];
    
    $sql = "UPDATE payments 
            SET payment_method_id = :payment_method_id, 
                amount = :amount, 
                transaction_id = :transaction_id, 
                status = 'pending',
                updated_at = NOW()
            WHERE id = :payment_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':payment_id', $payment_id);
    $stmt->bindParam(':payment_method_id', $payment_method_id);
    $stmt->bindParam(':amount', $total_price);
    $stmt->bindParam(':transaction_id', $order_id);
    $stmt->execute();
} else {
    // No payment exists, create new one
    $sql = "INSERT INTO payments (reservation_id, payment_method_id, amount, transaction_id, status) 
            VALUES (:reservation_id, :payment_method_id, :amount, :transaction_id, 'pending')";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':reservation_id', $reservation_id);
    $stmt->bindParam(':payment_method_id', $payment_method_id);
    $stmt->bindParam(':amount', $total_price);
    $stmt->bindParam(':transaction_id', $order_id);
    $stmt->execute();
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Checkout - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <!-- Favicon -->
    <link href="../img/logo.png" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet" />

    <style>
        :root {
            --primary-color: #4dc387;
            --primary-dark: #3da876;
            --primary-light: #e8f5f0;
            --white: #ffffff;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --gray-text: #6c757d;
        }

        body {
            background-color: var(--light-bg);
        }

        .checkout-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }

        .checkout-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .checkout-body {
            padding: 30px;
        }

        .order-summary {
            background: var(--light-bg);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            align-items: center;
        }

        .summary-row strong {
            color: var(--dark-text);
        }

        .summary-row.total {
            border-top: 2px solid rgba(0, 0, 0, 0.1);
            padding-top: 15px;
            margin-top: 15px;
            font-size: 1.1rem;
        }

        .total-amount {
            color: var(--primary-color);
            font-weight: 700;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
        }

        .loading-spinner {
            border: 4px solid var(--light-bg);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- Topbar Start -->
    <?php include_once '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include_once '../includes/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- Checkout Container -->
    <div class="checkout-container">
        <div class="checkout-card">
            <div class="checkout-header">
                <i class="fas fa-credit-card fa-3x mb-3"></i>
                <h2>Pembayaran Reservasi</h2>
                <p class="mb-0">Tirta Sanita Outbound</p>
            </div>

            <div class="checkout-body">
                <!-- Order Summary -->
                <div class="order-summary">
                    <h5 class="mb-3">Ringkasan Pesanan</h5>
                    <div class="summary-row">
                        <span>Paket:</span>
                        <strong><?php echo htmlspecialchars($package_name); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Tanggal Kunjungan:</span>
                        <strong><?php echo date('d/m/Y', strtotime($visit_date)); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Jumlah Orang:</span>
                        <strong><?php echo $num_visitors; ?> orang</strong>
                    </div>
                    <div class="summary-row">
                        <span>Harga per Orang:</span>
                        <strong>Rp <?php echo number_format($price_per_person, 0, ',', '.'); ?></strong>
                    </div>
                    <div class="summary-row total">
                        <span>Total Pembayaran:</span>
                        <span class="total-amount">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <!-- Loading Message -->
                <div class="loading" id="loading">
                    <div class="loading-spinner"></div>
                    <p>Memproses pembayaran Anda...</p>
                </div>

                <!-- Midtrans SNAP Button -->
                <div id="snap-container"></div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include_once '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Midtrans SNAP Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo MidtransConfig::CLIENT_KEY; ?>"></script>

    <script>
        // Trigger SNAP payment
        document.addEventListener('DOMContentLoaded', function() {
            const snapToken = '<?php echo $snap_token; ?>';
            
            if (snapToken) {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        // Pembayaran berhasil
                        window.location.href = 'payment-success.php?order_id=<?php echo $order_id; ?>';
                    },
                    onPending: function(result) {
                        // Pembayaran pending
                        window.location.href = 'payment-pending.php?order_id=<?php echo $order_id; ?>';
                    },
                    onError: function(result) {
                        // Pembayaran error
                        window.location.href = 'payment-error.php?order_id=<?php echo $order_id; ?>';
                    },
                    onClose: function() {
                        // User menutup modal tanpa menyelesaikan pembayaran
                        alert('Pembayaran dibatalkan.');
                        window.history.back();
                    }
                });
            }
        });
    </script>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
