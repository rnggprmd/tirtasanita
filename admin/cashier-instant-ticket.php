<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is cashier/admin
if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

// Handle instant ticket purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'purchase_ticket') {
        try {
            $customer_name = $_POST['customer_name'];
            $customer_phone = $_POST['customer_phone'];
            $package_id = $_POST['package_id'];
            $quantity = intval($_POST['quantity']);
            $payment_method_id = $_POST['payment_method_id'];
            $notes = $_POST['notes'] ?? '';
            
            // Validate inputs
            if (empty($customer_name) || empty($customer_phone) || empty($package_id) || $quantity <= 0) {
                throw new Exception('Data tidak lengkap.');
            }
            
            // Get package info for price calculation
            $pkg_query = "SELECT price_weekday, price_weekend FROM packages WHERE id = :id";
            $pkg_stmt = $db->prepare($pkg_query);
            $pkg_stmt->bindParam(':id', $package_id);
            $pkg_stmt->execute();
            $package = $pkg_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$package) {
                throw new Exception('Paket tidak ditemukan.');
            }
            
            // Determine if today is weekday or weekend
            $today = date('Y-m-d');
            $day_of_week = date('N', strtotime($today));
            $is_weekday = ($day_of_week >= 1 && $day_of_week <= 5);
            $price_per_ticket = $is_weekday ? $package['price_weekday'] : $package['price_weekend'];
            $total_price = $price_per_ticket * $quantity;
            
            // Check if customer exists
            $user_check_sql = "SELECT id FROM users WHERE whatsapp = :phone AND role = 'user'";
            $user_check_stmt = $db->prepare($user_check_sql);
            $user_check_stmt->bindParam(':phone', $customer_phone);
            $user_check_stmt->execute();
            $existing_user = $user_check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing_user) {
                $user_id = $existing_user['id'];
            } else {
                // Create new user for instant ticket customer
                $user_insert_sql = "INSERT INTO users (name, whatsapp, role, created_at) 
                                   VALUES (:name, :whatsapp, 'user', NOW())";
                $user_insert_stmt = $db->prepare($user_insert_sql);
                $user_insert_stmt->bindParam(':name', $customer_name);
                $user_insert_stmt->bindParam(':whatsapp', $customer_phone);
                $user_insert_stmt->execute();
                $user_id = $db->lastInsertId();
            }
            
            // Create reservation for instant ticket
            $res_sql = "INSERT INTO reservations (user_id, package_id, visit_date, num_visitors, total_price, is_weekday, notes, status)
                       VALUES (:user_id, :package_id, :visit_date, :num_visitors, :total_price, :is_weekday, :notes, 'confirmed')";
            $res_stmt = $db->prepare($res_sql);
            $res_stmt->bindParam(':user_id', $user_id);
            $res_stmt->bindParam(':package_id', $package_id);
            $res_stmt->bindParam(':visit_date', $today);
            $res_stmt->bindParam(':num_visitors', $quantity);
            $res_stmt->bindParam(':total_price', $total_price);
            $res_stmt->bindParam(':is_weekday', $is_weekday);
            $res_stmt->bindParam(':notes', $notes);
            $res_stmt->execute();
            
            $reservation_id = $db->lastInsertId();
            
            // Create payment record
            $order_id = 'INSTANT-' . $reservation_id . '-' . time();
            $pay_sql = "INSERT INTO payments (reservation_id, payment_method_id, amount, transaction_id, status)
                       VALUES (:reservation_id, :method_id, :amount, :transaction_id, 'completed')";
            $pay_stmt = $db->prepare($pay_sql);
            $pay_stmt->bindParam(':reservation_id', $reservation_id);
            $pay_stmt->bindParam(':method_id', $payment_method_id);
            $pay_stmt->bindParam(':amount', $total_price);
            $pay_stmt->bindParam(':transaction_id', $order_id);
            $pay_stmt->execute();
            
            // Redirect to print ticket
            redirect("print-ticket.php?id=" . $reservation_id . "&type=instant");
            
        } catch (Exception $e) {
            setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }
    }
}

// Get packages for dropdown
$packages_query = "SELECT id, name, price_weekday, price_weekend FROM packages ORDER BY name";
$packages_stmt = $db->prepare($packages_query);
$packages_stmt->execute();
$packages = $packages_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment methods
$pm_sql = "SELECT * FROM payment_methods WHERE id != 7 ORDER BY id";
$pm_stmt = $db->prepare($pm_sql);
$pm_stmt->execute();
$payment_methods = $pm_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get today's price reference
$today = date('Y-m-d');
$day_of_week = date('N', strtotime($today));
$is_weekday = ($day_of_week >= 1 && $day_of_week <= 5);
$day_type = $is_weekday ? 'Weekday' : 'Weekend';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Penjualan Tiket Langsung - Kasir</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="../img/logo.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
            font-family: 'Open Sans', sans-serif;
            background-color: var(--light-bg);
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: var(--white);
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            background-color: var(--white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .sidebar .sidebar-header {
            padding: 20px;
            background-color: var(--primary-color);
            color: var(--white);
        }

        .sidebar .sidebar-header h3 {
            margin: 0;
        }

        .sidebar .sidebar-header p {
            margin: 0;
            font-size: 0.85rem;
        }

        .sidebar .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            color: var(--dark-text);
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-light);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .navbar {
            background-color: var(--white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .form-select, .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(77, 195, 135, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
        }

        .price-summary {
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0fdf8 100%);
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .price-summary .row {
            align-items: center;
        }

        .price-summary .price-item {
            text-align: center;
            padding: 15px 0;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
        }

        .price-summary .price-item:last-child {
            border-right: none;
        }

        .price-summary .label {
            font-size: 0.9rem;
            color: var(--gray-text);
            margin-bottom: 5px;
        }

        .price-summary .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .price-summary .total {
            font-size: 2rem;
            color: var(--primary-dark);
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .badge {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <?php require_once 'sidebar-helper.php'; 
    echo generateSidebar('cashier-instant-ticket.php');
    ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="cashier-dashboard.php">Kasir</a>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-outline-primary" href="cashier-dashboard.php">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1><i class="fas fa-ticket-alt me-2"></i>Penjualan Tiket Langsung</h1>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Form Pembelian Tiket</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <!-- Data Pelanggan -->
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi Penting:</strong> Tiket hanya berlaku untuk hari ini (<?php echo date('d M Y', strtotime($today)); ?>)
                                </div>

                                <h6 class="mb-3"><i class="fas fa-user-circle me-2"></i>Data Pelanggan</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="customer_name" class="form-label">Nama Pelanggan *</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" required placeholder="Masukkan nama lengkap">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="customer_phone" class="form-label">Nomor WhatsApp *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required placeholder="62xxxxxxxxxx">
                                    </div>
                                </div>

                                <hr>

                                <h6 class="mb-3"><i class="fas fa-box me-2"></i>Pilih Paket & Jumlah</h6>

                                <div class="mb-4">
                                    <label for="package_id" class="form-label">Paket Wisata *</label>
                                    <select class="form-select" id="package_id" name="package_id" required onchange="updatePricePreview()">
                                        <option value="">-- Pilih Paket --</option>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?php echo $pkg['id']; ?>" 
                                                    data-weekday="<?php echo $pkg['price_weekday']; ?>" 
                                                    data-weekend="<?php echo $pkg['price_weekend']; ?>">
                                                <?php echo $pkg['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="quantity" class="form-label">Jumlah Tiket *</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="999" required onchange="updatePricePreview()">
                                </div>

                                <div class="mb-4">
                                    <label for="payment_method_id" class="form-label">Metode Pembayaran *</label>
                                    <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <?php foreach ($payment_methods as $pm): ?>
                                            <option value="<?php echo $pm['id']; ?>"><?php echo $pm['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan khusus untuk tiket..."></textarea>
                                </div>

                                <!-- Price Summary -->
                                <div class="price-summary">
                                    <div class="row text-center">
                                        <div class="col-md-4 price-item">
                                            <div class="label">Harga per Tiket</div>
                                            <div class="value" id="price_per_ticket">Rp 0</div>
                                        </div>
                                        <div class="col-md-4 price-item">
                                            <div class="label">Jumlah Tiket</div>
                                            <div class="value" id="qty_display">1</div>
                                        </div>
                                        <div class="col-md-4 price-item">
                                            <div class="label">Total Pembayaran</div>
                                            <div class="total" id="total_display">Rp 0</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" name="action" value="purchase_ticket" class="btn btn-success btn-lg">
                                        <i class="fas fa-check me-2"></i> Proses & Cetak Tiket
                                    </button>
                                    <a href="cashier-dashboard.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Panduan</h5>
                        </div>
                        <div class="card-body">
                            <h6>Langkah-langkah:</h6>
                            <ol class="small">
                                <li>Masukkan data pelanggan</li>
                                <li>Pilih paket wisata</li>
                                <li>Tentukan jumlah tiket</li>
                                <li>Pilih metode pembayaran</li>
                                <li>Klik "Proses & Cetak"</li>
                                <li>Tiket akan langsung tercetak</li>
                            </ol>

                            <hr>

                            <h6>Info Harga Hari Ini:</h6>
                            <div class="alert alert-info mb-3">
                                <strong><?php echo $day_type ?></strong><br>
                                <small><?php echo date('l, d F Y', strtotime($today)); ?></small>
                            </div>

                            <h6>Status Tiket:</h6>
                            <ul class="small">
                                <li>✓ Langsung berlaku hari ini</li>
                                <li>✓ Tidak bisa dipindahkan</li>
                                <li>✓ Tunai saat pembelian</li>
                                <li>✓ Pelanggan baru otomatis terdaftar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePricePreview() {
            const packageSelect = document.getElementById('package_id');
            const quantity = parseInt(document.getElementById('quantity').value) || 0;

            if (!packageSelect.value || quantity <= 0) {
                document.getElementById('price_per_ticket').textContent = 'Rp 0';
                document.getElementById('qty_display').textContent = quantity;
                document.getElementById('total_display').textContent = 'Rp 0';
                return;
            }

            const option = packageSelect.options[packageSelect.selectedIndex];
            const weekdayPrice = parseFloat(option.dataset.weekday);
            const weekendPrice = parseFloat(option.dataset.weekend);

            // Determine if today is weekday or weekend
            const today = new Date();
            const dayOfWeek = today.getDay();
            const isWeekday = dayOfWeek >= 1 && dayOfWeek <= 5;
            const pricePerTicket = isWeekday ? weekdayPrice : weekendPrice;
            const totalPrice = pricePerTicket * quantity;

            document.getElementById('price_per_ticket').textContent = 'Rp ' + pricePerTicket.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            document.getElementById('qty_display').textContent = quantity;
            document.getElementById('total_display').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });

        // Initialize price preview on page load
        updatePricePreview();
    </script>
</body>

</html>
