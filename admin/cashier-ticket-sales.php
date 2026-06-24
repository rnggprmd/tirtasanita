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

// Get today's date
$today = date('Y-m-d');

// Handle ticket sale
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'process_ticket_sale') {
        $user_id = null;
        $customer_name = trim($_POST['customer_name'] ?? '');
        $customer_whatsapp = trim($_POST['customer_whatsapp'] ?? '');
        $customer_email = trim($_POST['customer_email'] ?? '');
        $package_id = $_POST['package_id'];
        $num_visitors = $_POST['num_visitors'];
        $payment_method_id = $_POST['payment_method_id'];
        $notes = $_POST['notes'] ?? '';
        
        try {
            // Validasi data pelanggan
            if (empty($customer_name) || empty($customer_whatsapp)) {
                throw new Exception('Nama dan nomor WhatsApp pelanggan harus diisi!');
            }
            
            // Check if user with same whatsapp exists
            $check_sql = "SELECT id FROM users WHERE whatsapp = :whatsapp LIMIT 1";
            $check_stmt = $db->prepare($check_sql);
            $check_stmt->bindParam(':whatsapp', $customer_whatsapp);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                // User exists, use that ID
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
                $user_id = $existing['id'];
            } else {
                // Create new user
                $insert_sql = "INSERT INTO users (name, whatsapp, email, role, password, status, created_at) 
                              VALUES (:name, :whatsapp, :email, 'user', :password, 'active', NOW())";
                $insert_stmt = $db->prepare($insert_sql);
                $password_hash = password_hash('default123', PASSWORD_BCRYPT);
                $insert_stmt->bindParam(':name', $customer_name);
                $insert_stmt->bindParam(':whatsapp', $customer_whatsapp);
                $insert_stmt->bindParam(':email', $customer_email);
                $insert_stmt->bindParam(':password', $password_hash);
                $insert_stmt->execute();
                $user_id = $db->lastInsertId();
            }
            
            // Get package price
            $pkg_query = "SELECT price_weekday, price_weekend FROM packages WHERE id = :id";
            $pkg_stmt = $db->prepare($pkg_query);
            $pkg_stmt->bindParam(':id', $package_id);
            $pkg_stmt->execute();
            $package = $pkg_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$package) {
                throw new Exception('Paket tidak ditemukan!');
            }
            
            // Check if today is weekday or weekend
            $day_of_week = date('N');
            $is_weekday = ($day_of_week >= 1 && $day_of_week <= 5);
            $price_per_person = $is_weekday ? $package['price_weekday'] : $package['price_weekend'];
            $total_price = $price_per_person * $num_visitors;
            
            // Create reservation with status 'confirmed' (direct sale)
            $res_sql = "INSERT INTO reservations (user_id, package_id, visit_date, num_visitors, total_price, is_weekday, notes, status, created_at)
                       VALUES (:user_id, :package_id, :visit_date, :num_visitors, :total_price, :is_weekday, :notes, 'confirmed', NOW())";
            $res_stmt = $db->prepare($res_sql);
            $res_stmt->bindParam(':user_id', $user_id);
            $res_stmt->bindParam(':package_id', $package_id);
            $res_stmt->bindParam(':visit_date', $today);
            $res_stmt->bindParam(':num_visitors', $num_visitors);
            $res_stmt->bindParam(':total_price', $total_price);
            $res_stmt->bindParam(':is_weekday', $is_weekday);
            $res_stmt->bindParam(':notes', $notes);
            $res_stmt->execute();
            
            $reservation_id = $db->lastInsertId();
            
            // Create payment record
            $order_id = 'TICKET-' . $reservation_id . '-' . time();
            $pay_sql = "INSERT INTO payments (reservation_id, payment_method_id, amount, transaction_id, status, created_at)
                       VALUES (:reservation_id, :method_id, :amount, :transaction_id, 'completed', NOW())";
            $pay_stmt = $db->prepare($pay_sql);
            $pay_stmt->bindParam(':reservation_id', $reservation_id);
            $pay_stmt->bindParam(':method_id', $payment_method_id);
            $pay_stmt->bindParam(':amount', $total_price);
            $pay_stmt->bindParam(':transaction_id', $order_id);
            $pay_stmt->execute();
            
            setFlashMessage('message', 'Tiket berhasil dijual! ID: #' . $reservation_id, 'alert alert-success');
            redirect("cashier-ticket-detail.php?id=" . $reservation_id);
            
        } catch (Exception $e) {
            setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }
    }
}

// Get packages
$packages_query = "SELECT id, name, price_weekday, price_weekend FROM packages ORDER BY name";
$packages_stmt = $db->prepare($packages_query);
$packages_stmt->execute();
$packages = $packages_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment methods
$pm_sql = "SELECT * FROM payment_methods WHERE id != 7 ORDER BY id";
$pm_stmt = $db->prepare($pm_sql);
$pm_stmt->execute();
$payment_methods = $pm_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Penjualan Tiket - Kasir</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="../img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('admin-style.css');
    </style>
</head>

<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar('cashier-ticket-sales.php'); ?>

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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>Penjualan Tiket Langsung</h1>
                            <small class="text-muted">Tanggal hari ini: <strong><?php echo date('d M Y', strtotime($today)); ?></strong></small>
                        </div>
                        <span class="ticket-badge">
                            <i class="fas fa-ticket-alt me-2"></i>Tiket Instant
                        </span>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Penjualan Tiket</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <!-- Data Pelanggan -->
                                <div class="customer-section">
                                    <h6 class="mb-3"><i class="fas fa-user me-2"></i>Data Pelanggan</h6>
                                    
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">
                                            <i class="fas fa-user-circle me-2"></i>Nama Pelanggan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               placeholder="Masukkan nama pelanggan" required>
                                        <small class="text-muted d-block mt-2">
                                            Isi nama lengkap pengunjung
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="customer_whatsapp" class="form-label">
                                            <i class="fab fa-whatsapp me-2"></i>Nomor WhatsApp <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="customer_whatsapp" name="customer_whatsapp" 
                                               placeholder="Contoh: 08123456789" required>
                                        <small class="text-muted d-block mt-2">
                                            Format: 08xxx atau +62xxx (untuk komunikasi dan pengiriman tiket)
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="customer_email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email (Opsional)
                                        </label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                               placeholder="Masukkan email (opsional)">
                                    </div>
                                </div>

                                <!-- Pilih Paket -->
                                <div class="mb-4">
                                    <label for="package_id" class="form-label">
                                        <i class="fas fa-box me-2"></i>Pilih Paket <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="package_id" name="package_id" required onchange="updatePrice()">
                                        <option value="">-- Pilih Paket --</option>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?php echo $pkg['id']; ?>" 
                                                    data-weekday="<?php echo $pkg['price_weekday']; ?>" 
                                                    data-weekend="<?php echo $pkg['price_weekend']; ?>"
                                                    data-name="<?php echo $pkg['name']; ?>">
                                                <?php echo $pkg['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Jumlah Tiket -->
                                <div class="mb-4">
                                    <label for="num_visitors" class="form-label">
                                        <i class="fas fa-ticket-alt me-2"></i>Jumlah Tiket <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="num_visitors" name="num_visitors" 
                                               value="1" min="1" max="100" required onchange="updatePrice()" 
                                               style="font-weight: 600; font-size: 1.1em;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Metode Pembayaran -->
                                <div class="mb-4">
                                    <label for="payment_method_id" class="form-label">
                                        <i class="fas fa-credit-card me-2"></i>Metode Pembayaran <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <?php foreach ($payment_methods as $pm): ?>
                                            <option value="<?php echo $pm['id']; ?>"><?php echo $pm['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Catatan -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Catatan (Opsional)
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan khusus..."></textarea>
                                </div>

                                <!-- Price Preview -->
                                <div class="price-preview">
                                    <div class="row text-center">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">Harga per Tiket</small>
                                            <div class="h5" id="price_per_ticket">Rp 0</div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">Total Harga</small>
                                            <div class="h4 text-primary fw-bold" id="total_price">Rp 0</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" name="action" value="process_ticket_sale" class="btn btn-success btn-lg">
                                        <i class="fas fa-check me-2"></i> Jual Tiket & Terima Pembayaran
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
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                        </div>
                        <div class="card-body">
                            <h6><i class="fas fa-ticket-alt me-2"></i>Tentang Tiket Instant:</h6>
                            <ul class="small">
                                <li>Untuk pengunjung walk-in yang langsung masuk hari ini</li>
                                <li>Bukan untuk booking di tanggal lain</li>
                                <li>Pembayaran langsung, tiket langsung tercetak</li>
                                <li>Status otomatis: Confirmed</li>
                                <li>Bisa beli multiple tiket sekaligus</li>
                            </ul>

                            <hr>

                            <h6><i class="fas fa-star me-2"></i>Fitur:</h6>
                            <ul class="small">
                                <li>✅ Input data pelanggan baru</li>
                                <li>✅ Hitung otomatis harga weekday/weekend</li>
                                <li>✅ Preview total harga real-time</li>
                                <li>✅ Cetak tiket langsung</li>
                                <li>✅ Kirim tiket via WhatsApp</li>
                            </ul>

                            <hr>

                            <h6><i class="fas fa-lightbulb me-2"></i>Catatan:</h6>
                            <p class="small mb-0">
                                <strong>Nama & WhatsApp harus diisi!</strong> Data ini akan digunakan untuk membuat akun pelanggan dan mengirim tiket.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePrice() {
            const packageSelect = document.getElementById('package_id');
            const numTickets = parseInt(document.getElementById('num_visitors').value) || 0;

            if (!packageSelect.value) {
                document.getElementById('price_per_ticket').textContent = 'Rp 0';
                document.getElementById('total_price').textContent = 'Rp 0';
                return;
            }

            const option = packageSelect.options[packageSelect.selectedIndex];
            const weekdayPrice = parseFloat(option.dataset.weekday);
            const weekendPrice = parseFloat(option.dataset.weekend);

            // Check if today is weekday or weekend
            const today = new Date();
            const dayOfWeek = today.getDay();
            const isWeekday = dayOfWeek >= 1 && dayOfWeek <= 5;
            const pricePerTicket = isWeekday ? weekdayPrice : weekendPrice;
            const totalPrice = pricePerTicket * numTickets;

            document.getElementById('price_per_ticket').textContent = 'Rp ' + pricePerTicket.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            document.getElementById('total_price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        function increaseQty() {
            const input = document.getElementById('num_visitors');
            input.value = Math.min(parseInt(input.value) + 1, 100);
            updatePrice();
        }

        function decreaseQty() {
            const input = document.getElementById('num_visitors');
            input.value = Math.max(parseInt(input.value) - 1, 1);
            updatePrice();
        }

        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });
    </script>
</body>

</html>
