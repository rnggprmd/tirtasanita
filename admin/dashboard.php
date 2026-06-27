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

// Get statistics
// Total users
$sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$stmt = $db->prepare($sql);
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Total packages
$sql = "SELECT COUNT(*) as total_packages FROM packages";
$stmt = $db->prepare($sql);
$stmt->execute();
$total_packages = $stmt->fetch(PDO::FETCH_ASSOC)['total_packages'];

// Total reservations
$sql = "SELECT COUNT(*) as total_reservations FROM reservations";
$stmt = $db->prepare($sql);
$stmt->execute();
$total_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['total_reservations'];

// Pending reservations
$sql = "SELECT COUNT(*) as pending_reservations FROM reservations WHERE status = 'pending'";
$stmt = $db->prepare($sql);
$stmt->execute();
$pending_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['pending_reservations'];

// Recent reservations
$sql = "SELECT r.*, u.name as user_name, u.whatsapp, p.name as package_name 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        JOIN packages p ON r.package_id = p.id 
        ORDER BY r.created_at DESC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute();
$recent_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Tirta Sanita Outbound, Admin, Dashboard" name="keywords">
    <meta content="Admin panel untuk mengelola website Tirta Sanita Outbound" name="description">

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
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar(basename($_SERVER['PHP_SELF'])); ?>

    <!-- Main Content Start -->
    <div class="main-content">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand d-none d-lg-block" href="dashboard.php">
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
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
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
                        <h1 class="mb-0">Dashboard Admin</h1>
                        <small class="text-muted">Sistem Manajemen Tirta Sanita</small>
                    </div>
                </div>
            </div>
            <?php displayFlashMessage(); ?>

            <!-- Statistics Section -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-info">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Pengguna</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-success">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $total_packages; ?></h3>
                            <p>Total Paket</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-card">
                        <div class="icon bg-warning">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $total_reservations; ?></h3>
                            <p>Total Reservasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="icon bg-danger">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo $pending_reservations; ?></h3>
                            <p>Reservasi Pending</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statistics Section End -->

            <!-- Recent Reservations Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i> Reservasi Terbaru
                            </h5>
                            <a href="reservations.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right me-1"></i> Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_reservations)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">Belum ada reservasi</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pengguna</th>
                                                <th>Paket</th>
                                                <th>Tanggal Kunjungan</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_reservations as $reservation): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $reservation['id']; ?></strong></td>
                                                    <td>
                                                        <strong><?php echo $reservation['user_name']; ?></strong><br>
                                                        <small class="text-muted"><?php echo $reservation['whatsapp']; ?></small>
                                                    </td>
                                                    <td><?php echo $reservation['package_name']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></td>
                                                    <td><strong><?php echo formatCurrency($reservation['total_price']); ?></strong></td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        switch ($reservation['status']) {
                                                            case 'pending':
                                                                $status_class = 'badge bg-warning';
                                                                $status_text = 'Menunggu Pembayaran';
                                                                break;
                                                            case 'confirmed':
                                                                $status_class = 'badge bg-success';
                                                                $status_text = 'Terkonfirmasi';
                                                                break;
                                                            case 'cancelled':
                                                                $status_class = 'badge bg-danger';
                                                                $status_text = 'Dibatalkan';
                                                                break;
                                                            case 'completed':
                                                                $status_class = 'badge bg-info';
                                                                $status_text = 'Selesai';
                                                                break;
                                                            default:
                                                                $status_class = 'badge bg-secondary';
                                                                $status_text = $reservation['status'];
                                                        }
                                                        ?>
                                                        <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="reservation-detail.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
            <!-- Recent Reservations Section End -->
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
