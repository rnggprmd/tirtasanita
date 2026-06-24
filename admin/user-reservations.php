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

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID pengguna tidak valid.', 'alert alert-danger');
    redirect("users.php");
}

$user_id = $_GET['id'];

// Get user details
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Pengguna tidak ditemukan.', 'alert alert-danger');
    redirect("users.php");
}

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's reservations
$sql = "SELECT r.*, p.name as package_name, 
        CASE 
            WHEN r.status = 'pending' THEN 'Menunggu Pembayaran'
            WHEN r.status = 'confirmed' THEN 'Dikonfirmasi'
            WHEN r.status = 'completed' THEN 'Selesai'
            WHEN r.status = 'cancelled' THEN 'Dibatalkan'
            ELSE r.status
        END as status_text,
        CASE 
            WHEN r.status = 'pending' THEN 'warning'
            WHEN r.status = 'confirmed' THEN 'success'
            WHEN r.status = 'completed' THEN 'info'
            WHEN r.status = 'cancelled' THEN 'danger'
            ELSE 'secondary'
        END as status_class,
        (SELECT COUNT(*) FROM payments WHERE reservation_id = r.id) as has_payment
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        WHERE r.user_id = :user_id
        ORDER BY r.visit_date DESC, r.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">


<head>
    <meta charset="utf-8">
    <title>Reservasi Pengguna - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Reservasi Pengguna" name="keywords">
    <meta content="Admin panel untuk mengelola reservasi pengguna di Taman Kopses Ciseeng" name="description">

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

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .facility-check {
            margin-bottom: 10px;
        }

        .facility-check .form-check-label {
            display: flex;
            align-items: center;
        }

        .facility-check .facility-icon {
            margin-right: 10px;
            width: 24px;
            text-align: center;
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

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Reservasi Pengguna</h1>
                    <div>
                        <a href="users.php" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
                        </a>
                        <a href="user-edit.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Pengguna
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">Nama Lengkap</label>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <h5 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h5>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">WhatsApp</label>
                                        <div class="d-flex align-items-center">
                                            <i class="fab fa-whatsapp me-2 text-success"></i>
                                            <?php
                                            // Format WhatsApp number
                                            $whatsapp = preg_replace('/[^0-9]/', '', $user['whatsapp']);
                                            if (substr($whatsapp, 0, 1) == '0') {
                                                $whatsapp = '62' . substr($whatsapp, 1);
                                            }
                                            if (substr($whatsapp, 0, 2) != '62') {
                                                $whatsapp = '62' . $whatsapp;
                                            }
                                            ?>
                                            <a href="https://wa.me/<?php echo $whatsapp; ?>" target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($user['whatsapp']); ?> <i class="fas fa-external-link-alt fs-6 ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <?php if (!empty($user['email'])): ?>
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">Email</label>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope me-2 text-primary"></i>
                                            <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">Peran</label>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tag me-2 text-primary"></i>
                                            <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?> px-3 py-2">
                                                <?php echo $user['role'] == 'admin' ? 'Admin' : 'Pengguna'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted mb-1">Terdaftar Pada</label>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-plus me-2 text-primary"></i>
                                            <?php echo date('d M Y H:i', strtotime($user['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Daftar Reservasi</h5>
                            <span class="badge bg-light text-dark px-3 py-2"><?php echo count($reservations); ?> Reservasi</span>
                        </div>
                        <div class="card-body">
                            <?php if (count($reservations) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tanggal Kunjungan</th>
                                                <th>Paket</th>
                                                <th>Jumlah Pengunjung</th>
                                                <th>Total Harga</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations as $reservation): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $reservation['id']; ?></strong></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="far fa-calendar-alt me-2 text-primary"></i>
                                                            <?php echo date('d M Y', strtotime($reservation['visit_date'])); ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($reservation['package_name']); ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-users me-2 text-primary"></i>
                                                            <?php echo $reservation['num_visitors']; ?> orang
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                                            <strong>Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $reservation['status_class']; ?> px-3 py-2">
                                                            <?php echo $reservation['status_text']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($reservation['has_payment'] > 0): ?>
                                                            <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> Ada</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary px-3 py-2"><i class="fas fa-times-circle me-1"></i> Belum Ada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="reservation-detail.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if ($reservation['has_payment'] > 0): ?>
                                                            <a href="send-ticket.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Kirim Tiket">
                                                                <i class="fas fa-ticket-alt"></i>
                                                            </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3 fs-4"></i>
                                    <div>
                                        <h6 class="mb-0">Tidak Ada Reservasi</h6>
                                        <p class="mb-0">Pengguna ini belum memiliki reservasi.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>

</html>
