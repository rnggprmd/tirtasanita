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

// Get packages and users for dropdown
$packages_query = "SELECT id, name, price_weekday, price_weekend FROM packages ORDER BY name";
$packages_stmt = $db->prepare($packages_query);
$packages_stmt->execute();
$packages = $packages_stmt->fetchAll(PDO::FETCH_ASSOC);

$users_query = "SELECT id, name, whatsapp FROM users WHERE role = 'user' ORDER BY name";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $package_id = $_POST['package_id'];
    $visit_date = $_POST['visit_date'];
    $num_visitors = $_POST['num_visitors'];
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Get package price
        $pkg_query = "SELECT price_weekday, price_weekend FROM packages WHERE id = :id";
        $pkg_stmt = $db->prepare($pkg_query);
        $pkg_stmt->bindParam(':id', $package_id);
        $pkg_stmt->execute();
        $package = $pkg_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if weekday or weekend
        $day_of_week = date('N', strtotime($visit_date));
        $is_weekday = ($day_of_week >= 1 && $day_of_week <= 5);
        $price_per_person = $is_weekday ? $package['price_weekday'] : $package['price_weekend'];
        $total_price = $price_per_person * $num_visitors;
        
        // Create reservation
        $res_sql = "INSERT INTO reservations (user_id, package_id, visit_date, num_visitors, total_price, is_weekday, notes, status)
                   VALUES (:user_id, :package_id, :visit_date, :num_visitors, :total_price, :is_weekday, :notes, 'pending')";
        $res_stmt = $db->prepare($res_sql);
        $res_stmt->bindParam(':user_id', $user_id);
        $res_stmt->bindParam(':package_id', $package_id);
        $res_stmt->bindParam(':visit_date', $visit_date);
        $res_stmt->bindParam(':num_visitors', $num_visitors);
        $res_stmt->bindParam(':total_price', $total_price);
        $res_stmt->bindParam(':is_weekday', $is_weekday);
        $res_stmt->bindParam(':notes', $notes);
        $res_stmt->execute();
        
        $reservation_id = $db->lastInsertId();
        
        setFlashMessage('message', 'Reservasi berhasil dibuat! ID: #' . $reservation_id, 'alert alert-success');
        redirect("cashier-reservations.php");
        
    } catch (Exception $e) {
        setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Buat Reservasi - Kasir</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="../img/logo.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin-style.css" rel="stylesheet">
</head>

<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar('cashier-add-reservation.php'); ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="cashier-dashboard.php">Kasir</a>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-outline-primary" href="cashier-reservations.php">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1>Buat Reservasi Baru</h1>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Form Reservasi</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <!-- Pilih Pengguna -->
                                <div class="mb-4">
                                    <label for="user_id" class="form-label">
                                        <i class="fas fa-user me-2"></i>Pilih Pengguna
                                    </label>
                                    <select class="form-select" id="user_id" name="user_id" required>
                                        <option value="">-- Pilih Pengguna --</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['id']; ?>">
                                                <?php echo $user['name']; ?> (<?php echo $user['whatsapp']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Pilih Paket -->
                                <div class="mb-4">
                                    <label for="package_id" class="form-label">
                                        <i class="fas fa-box me-2"></i>Pilih Paket
                                    </label>
                                    <select class="form-select" id="package_id" name="package_id" required onchange="updatePricePreview()">
                                        <option value="">-- Pilih Paket --</option>
                                        <?php foreach ($packages as $pkg): ?>
                                            <option value="<?php echo $pkg['id']; ?>" data-weekday="<?php echo $pkg['price_weekday']; ?>" data-weekend="<?php echo $pkg['price_weekend']; ?>">
                                                <?php echo $pkg['name']; ?> (Weekday: Rp <?php echo number_format($pkg['price_weekday'], 0, ',', '.'); ?> / Orang, Weekend: Rp <?php echo number_format($pkg['price_weekend'], 0, ',', '.'); ?> / Orang)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Tanggal Kunjungan -->
                                <div class="mb-4">
                                    <label for="visit_date" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Tanggal Kunjungan
                                    </label>
                                    <input type="date" class="form-control" id="visit_date" name="visit_date" required min="<?php echo date('Y-m-d'); ?>" onchange="updatePricePreview()">
                                </div>

                                <!-- Jumlah Pengunjung -->
                                <div class="mb-4">
                                    <label for="num_visitors" class="form-label">
                                        <i class="fas fa-users me-2"></i>Jumlah Pengunjung
                                    </label>
                                    <input type="number" class="form-control" id="num_visitors" name="num_visitors" value="1" min="1" max="100" required onchange="updatePricePreview()">
                                </div>

                                <!-- Catatan -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Catatan (Opsional)
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Catatan khusus untuk reservasi..."></textarea>
                                </div>

                                <!-- Price Preview -->
                                <div class="price-preview">
                                    <div class="row text-center">
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">Harga per Orang</small>
                                            <div class="h5" id="price_per_person">Rp 0</div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <small class="text-muted">Total Harga</small>
                                            <div class="h4 text-primary fw-bold" id="total_price">Rp 0</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i> Buat Reservasi
                                    </button>
                                    <a href="cashier-reservations.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Help Panel -->
                <div class="col-lg-4">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                        </div>
                        <div class="card-body">
                            <h6>Tentang Reservasi:</h6>
                            <ul class="small">
                                <li>Status awal: Pending</li>
                                <li>Tanggal minimal hari ini</li>
                                <li>Harga otomatis dihitung</li>
                                <li>Weekday: Senin - Jumat</li>
                                <li>Weekend: Sabtu - Minggu</li>
                            </ul>

                            <hr>

                            <h6>Setelah Membuat:</h6>
                            <ul class="small">
                                <li>Reservasi siap untuk pembayaran</li>
                                <li>Buka menu "Terima Pembayaran" untuk proses pembayaran</li>
                                <li>Setelah bayar → Status confirmed</li>
                                <li>Tiket bisa dicetak</li>
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
            const visitDate = document.getElementById('visit_date').value;
            const numVisitors = parseInt(document.getElementById('num_visitors').value) || 0;

            if (!packageSelect.value || !visitDate) {
                document.getElementById('price_per_person').textContent = 'Rp 0';
                document.getElementById('total_price').textContent = 'Rp 0';
                return;
            }

            const option = packageSelect.options[packageSelect.selectedIndex];
            const weekdayPrice = parseFloat(option.dataset.weekday);
            const weekendPrice = parseFloat(option.dataset.weekend);

            // Check if weekday or weekend
            const date = new Date(visitDate);
            const dayOfWeek = date.getDay();
            const isWeekday = dayOfWeek >= 1 && dayOfWeek <= 5;
            const pricePerPerson = isWeekday ? weekdayPrice : weekendPrice;
            const totalPrice = pricePerPerson * numVisitors;

            document.getElementById('price_per_person').textContent = 'Rp ' + pricePerPerson.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            document.getElementById('total_price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });
    </script>
</body>

</html>
