<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is cashier
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("../user/login.php");
}

if (!isCashier() && !isAdmin()) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

// Get today's statistics
$today = date('Y-m-d');

// Get pending payments
$sql = "SELECT COUNT(*) as total FROM payments WHERE status = 'pending' AND DATE(created_at) = :date";
$stmt = $db->prepare($sql);
$stmt->bindParam(':date', $today);
$stmt->execute();
$pending_payments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get completed payments today
$sql = "SELECT COUNT(*) as total, SUM(amount) as total_amount FROM payments WHERE status = 'completed' AND DATE(created_at) = :date";
$stmt = $db->prepare($sql);
$stmt->bindParam(':date', $today);
$stmt->execute();
$completed = $stmt->fetch(PDO::FETCH_ASSOC);
$completed_count = $completed['total'] ?? 0;
$completed_amount = $completed['total_amount'] ?? 0;

// Get total reservations today
$sql = "SELECT COUNT(*) as total FROM reservations WHERE DATE(created_at) = :date";
$stmt = $db->prepare($sql);
$stmt->bindParam(':date', $today);
$stmt->execute();
$today_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get recent transactions
$sql = "SELECT p.*, r.id as reservation_id, u.name as user_name, pm.name as payment_method_name
        FROM payments p
        JOIN reservations r ON p.reservation_id = r.id
        JOIN users u ON r.user_id = u.id
        JOIN payment_methods pm ON p.payment_method_id = pm.id
        ORDER BY p.created_at DESC
        LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->execute();
$recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Kasir - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Tirta Sanita Outbound, Kasir, Dashboard" name="keywords">
    <meta content="Dashboard kasir untuk mengelola pembayaran" name="description">

    <!-- Favicon -->
    <link href="../img/logo.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Admin Stylesheet -->
    <link href="admin-style.css" rel="stylesheet">
</head>

<body>
    <?php require_once 'sidebar-helper.php'; 
    echo generateSidebar('cashier-dashboard.php');
    ?>

    <!-- Main Content Start -->
    <div class="main-content">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand d-none d-lg-block" href="cashier-dashboard.php">
                    <span>Tirta Sanita Outbound</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Content Start -->
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="mb-0">Dashboard Kasir</h1>
                        <small class="text-muted">Status Pembayaran & Transaksi</small>
                    </div>
                </div>
            </div>
            <?php displayFlashMessage(); ?>

            <!-- Statistics Section -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-warning">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $pending_payments; ?></h3>
                            <p>Pembayaran Pending</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $completed_count; ?></h3>
                            <p>Pembayaran Selesai</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-info">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo formatCurrency($completed_amount); ?></h3>
                            <p>Total Hari Ini</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="icon bg-primary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $today_reservations; ?></h3>
                            <p>Reservasi Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statistics Section End -->

            <!-- Quick Actions Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i> Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="cashier-reservations.php" class="btn btn-primary">
                                    <i class="fas fa-calendar-alt me-2"></i> Kelola Reservasi
                                </a>
                                <a href="cashier-add-reservation.php" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i> Reservasi Baru
                                </a>
                                <a href="cashier-ticket-sales.php" class="btn btn-outline-primary">
                                    <i class="fas fa-ticket-alt me-2"></i> Penjualan Tiket
                                </a>
                                <a href="cashier-payments.php" class="btn btn-outline-primary">
                                    <i class="fas fa-credit-card me-2"></i> Terima Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quick Actions Section End -->

            <!-- Recent Transactions Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-exchange-alt me-2"></i> Transaksi Terbaru
                            </h5>
                            <a href="cashier-payments.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right me-1"></i> Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_transactions)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">Belum ada transaksi</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pengguna</th>
                                                <th>Reservasi</th>
                                                <th>Metode</th>
                                                <th>Jumlah</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_transactions as $trans): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $trans['id']; ?></strong></td>
                                                    <td><strong><?php echo $trans['user_name']; ?></strong></td>
                                                    <td>#<?php echo $trans['reservation_id']; ?></td>
                                                    <td><?php echo $trans['payment_method_name']; ?></td>
                                                    <td><strong><?php echo formatCurrency($trans['amount']); ?></strong></td>
                                                    <td><?php echo date('d M Y H:i', strtotime($trans['created_at'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        $status_text = '';
                                                        switch ($trans['status']) {
                                                            case 'pending':
                                                                $status_class = 'badge bg-warning';
                                                                $status_text = 'Pending';
                                                                break;
                                                            case 'completed':
                                                                $status_class = 'badge bg-success';
                                                                $status_text = 'Selesai';
                                                                break;
                                                            default:
                                                                $status_class = 'badge bg-secondary';
                                                                $status_text = $trans['status'];
                                                        }
                                                        ?>
                                                        <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Transactions Section End -->
        </div>
        <!-- Content End -->
    </div>
    <!-- Main Content End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
                $('.main-content').toggleClass('active');
            });

            // Auto-hide sidebar on mobile
            $(window).resize(function() {
                if ($(window).width() < 992) {
                    $('.sidebar').removeClass('active');
                    $('.main-content').removeClass('active');
                } else {
                    $('.sidebar').addClass('active');
                    $('.main-content').addClass('active');
                }
            }).trigger('resize');
        });
    </script>
</body>

</html>
