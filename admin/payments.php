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

// Process action requests
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    
    // Check if payment exists
    $sql = "SELECT * FROM payments WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        switch ($action) {
            case 'approve':
                $sql = "UPDATE payments SET status = 'completed' WHERE id = :id";
                $message = "Pembayaran berhasil disetujui.";
                
                // Also update reservation status
                $reservation_id = $payment['reservation_id'];
                $update_reservation = "UPDATE reservations SET status = 'confirmed' WHERE id = :id";
                $stmt_reservation = $db->prepare($update_reservation);
                $stmt_reservation->bindParam(':id', $reservation_id);
                $stmt_reservation->execute();
                break;
                
            case 'reject':
                $sql = "UPDATE payments SET status = 'failed' WHERE id = :id";
                $message = "Pembayaran berhasil ditolak.";
                break;
                
            default:
                redirect("payments.php");
        }
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            setFlashMessage('message', $message, 'alert alert-success');
        } else {
            setFlashMessage('message', 'Terjadi kesalahan saat memperbarui status pembayaran.', 'alert alert-danger');
        }
    } else {
        setFlashMessage('message', 'Pembayaran tidak ditemukan.', 'alert alert-danger');
    }
    
    redirect("payments.php");
}

// Set default filter values
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$query = "SELECT p.*, r.id as reservation_id, r.visit_date, r.total_price as reservation_price, 
          u.name as user_name, u.whatsapp, 
          pk.name as package_name, 
          pm.name as payment_method_name 
          FROM payments p 
          JOIN reservations r ON p.reservation_id = r.id 
          JOIN users u ON r.user_id = u.id 
          JOIN packages pk ON r.package_id = pk.id 
          JOIN payment_methods pm ON p.payment_method_id = pm.id 
          WHERE 1=1";

$params = [];

if (!empty($status_filter)) {
    $query .= " AND p.status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($date_filter)) {
    $query .= " AND DATE(p.created_at) = :date";
    $params[':date'] = $date_filter;
}

if (!empty($search)) {
    $query .= " AND (u.name LIKE :search OR u.whatsapp LIKE :search OR pk.name LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Pembayaran - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Pembayaran" name="keywords">
    <meta content="Admin panel untuk mengelola pembayaran di Taman Kopses Ciseeng" name="description">

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

        .table th {
            font-weight: 600;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 50px;
        }

        .payment-proof {
            max-width: 100px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .payment-proof:hover {
            transform: scale(1.05);
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
                    <h1 class="mb-4">Kelola Pembayaran</h1>
                    <?php displayFlashMessage(); ?>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filter Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                                        <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="failed" <?php echo ($status_filter == 'failed') ? 'selected' : ''; ?>>Gagal</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date" class="form-label">Tanggal Pembayaran</label>
                                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $date_filter; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>" placeholder="Nama, WhatsApp, atau Paket">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($payments)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Tidak ada pembayaran yang ditemukan</p>
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
                                                <th>Bukti</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($payments as $payment): ?>
                                                <tr>
                                                    <td>#<?php echo $payment['id']; ?></td>
                                                    <td>
                                                        <?php echo $payment['user_name']; ?><br>
                                                        <small class="text-muted"><?php echo $payment['whatsapp']; ?></small>
                                                    </td>
                                                    <td>
                                                        #<?php echo $payment['reservation_id']; ?><br>
                                                        <small class="text-muted"><?php echo $payment['package_name']; ?></small>
                                                    </td>
                                                    <td><?php echo $payment['payment_method_name']; ?></td>
                                                    <td><?php echo formatCurrency($payment['amount']); ?></td>
                                                    <td>
                                                        <?php if (!empty($payment['proof_of_payment'])): ?>
                                                            <img src="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" class="payment-proof" alt="Bukti Pembayaran" data-bs-toggle="modal" data-bs-target="#proofModal" data-img="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>">
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Tidak Ada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d M Y', strtotime($payment['created_at'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        switch ($payment['status']) {
                                                            case 'pending':
                                                                $status_class = 'badge bg-warning';
                                                                $status_text = 'Menunggu Konfirmasi';
                                                                break;
                                                            case 'completed':
                                                                $status_class = 'badge bg-success';
                                                                $status_text = 'Selesai';
                                                                break;
                                                            case 'failed':
                                                                $status_class = 'badge bg-danger';
                                                                $status_text = 'Gagal';
                                                                break;
                                                            case 'refunded':
                                                                $status_class = 'badge bg-info';
                                                                $status_text = 'Dikembalikan';
                                                                break;
                                                            default:
                                                                $status_class = 'badge bg-secondary';
                                                                $status_text = $payment['status'];
                                                        }
                                                        ?>
                                                        <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($payment['status'] == 'pending'): ?>
                                                            <div class="btn-group">
                                                                <a href="payments.php?action=approve&id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                                <a href="payments.php?action=reject&id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <a href="payment-detail.php?id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
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

    <!-- Proof Modal -->
    <div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proofModalLabel">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="proofImage" class="img-fluid" alt="Bukti Pembayaran">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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

            // Show proof image in modal
            $('#proofModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var imgSrc = button.data('img');
                var modal = $(this);
                modal.find('#proofImage').attr('src', imgSrc);
            });
        });
    </script>
</body>

</html>
