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
    <title>Admin Dashboard - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Dashboard" name="keywords">
    <meta content="Admin panel untuk mengelola website Taman Kopses Ciseeng" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

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
            display: flex;
            flex-direction: column;
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

        .sidebar .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar .sidebar-menu .nav-link {
            padding: 12px 20px;
            color: var(--dark-text);
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .sidebar-menu .nav-link:hover,
        .sidebar .sidebar-menu .nav-link.active {
            background-color: var(--primary-light);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar .sidebar-menu .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
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

        .stat-card {
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            background-color: var(--white);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: var(--white);
        }

        .stat-card .content h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .stat-card .content p {
            margin-bottom: 0;
            color: var(--gray-text);
        }

        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .text-info {
            color: #0dcaf0 !important;
        }

        .text-success {
            color: #198754 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .bg-info {
            background-color: #0dcaf0 !important;
        }

        .bg-success {
            background-color: #198754 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .navbar {
            background-color: var(--white);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }

        .navbar-brand img {
            width: 30px;
            margin-right: 10px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .dropdown-item.active {
            background-color: var(--primary-color);
        }

        .table th {
            font-weight: 600;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 50px;
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
            .main-content.active {
                margin-left: 250px;
            }
        }
    </style>
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
                    <span>Taman Kopses Ciseeng</span>
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
                    <h1 class="mb-4">Dashboard</h1>
                    <?php displayFlashMessage(); ?>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-4 mb-md-0">
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
                <div class="col-md-3 mb-4 mb-md-0">
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
                <div class="col-md-3 mb-4 mb-md-0">
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
                <div class="col-md-3">
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Reservasi Terbaru</h5>
                            <a href="reservations.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recent_reservations)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Belum ada reservasi</p>
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
                                                    <td>#<?php echo $reservation['id']; ?></td>
                                                    <td>
                                                        <?php echo $reservation['user_name']; ?><br>
                                                        <small class="text-muted"><?php echo $reservation['whatsapp']; ?></small>
                                                    </td>
                                                    <td><?php echo $reservation['package_name']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></td>
                                                    <td><?php echo formatCurrency($reservation['total_price']); ?></td>
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
                                                        <a href="reservation-detail.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-primary">Detail</a>
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
