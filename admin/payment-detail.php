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

// Check if payment ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID Pembayaran tidak valid.', 'alert alert-danger');
    redirect("payments.php");
}

$payment_id = $_GET['id'];

// Get payment details
$sql = "SELECT p.*, r.id as reservation_id, r.visit_date, r.num_visitors, r.total_price as reservation_price, 
        r.status as reservation_status, r.notes, r.created_at as reservation_date,
        u.id as user_id, u.name as user_name, u.whatsapp, u.email, 
        pk.id as package_id, pk.name as package_name, pk.description as package_description, 
        pm.name as payment_method_name 
        FROM payments p 
        JOIN reservations r ON p.reservation_id = r.id 
        JOIN users u ON r.user_id = u.id 
        JOIN packages pk ON r.package_id = pk.id 
        JOIN payment_methods pm ON p.payment_method_id = pm.id 
        WHERE p.id = :id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $payment_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Pembayaran tidak ditemukan.', 'alert alert-danger');
    redirect("payments.php");
}

$payment = $stmt->fetch(PDO::FETCH_ASSOC);

// Process action requests
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
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
            $sql = "UPDATE payments SET status = 'failed', notes = :notes WHERE id = :id";
            $message = "Pembayaran berhasil ditolak.";
            
            // Also update reservation status
            $reservation_id = $payment['reservation_id'];
            $update_reservation = "UPDATE reservations SET status = 'cancelled' WHERE id = :id";
            $stmt_reservation = $db->prepare($update_reservation);
            $stmt_reservation->bindParam(':id', $reservation_id);
            $stmt_reservation->execute();
            break;
            
        default:
            redirect("payment-detail.php?id=" . $payment_id);
    }
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $payment_id);
    
    if ($action == 'reject') {
        $notes = $_POST['notes'] ?? 'Pembayaran ditolak oleh admin';
        $stmt->bindParam(':notes', $notes);
    }
    
    if ($stmt->execute()) {
        setFlashMessage('message', $message, 'alert alert-success');
    } else {
        setFlashMessage('message', 'Terjadi kesalahan saat memperbarui status pembayaran.', 'alert alert-danger');
    }
    
    redirect("payment-detail.php?id=" . $payment_id);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Pembayaran - Taman Kopses Ciseeng</title>
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

        .payment-proof {
            max-width: 100%;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .payment-proof:hover {
            transform: scale(1.02);
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 14px;
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
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Detail Pembayaran #<?php echo $payment['id']; ?></h1>
                    <a href="payments.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Informasi Pembayaran</h5>
                            <?php 
                            $status_class = '';
                            switch ($payment['status']) {
                                case 'pending':
                                    $status_class = 'bg-warning';
                                    $status_text = 'Menunggu Konfirmasi';
                                    break;
                                case 'completed':
                                    $status_class = 'bg-success';
                                    $status_text = 'Selesai';
                                    break;
                                case 'failed':
                                    $status_class = 'bg-danger';
                                    $status_text = 'Gagal';
                                    break;
                                case 'refunded':
                                    $status_class = 'bg-info';
                                    $status_text = 'Dikembalikan';
                                    break;
                                default:
                                    $status_class = 'bg-secondary';
                                    $status_text = $payment['status'];
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?> status-badge"><?php echo $status_text; ?></span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">ID Pembayaran</div>
                                        <div>#<?php echo $payment['id']; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">ID Reservasi</div>
                                        <div>#<?php echo $payment['reservation_id']; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Metode Pembayaran</div>
                                        <div><?php echo $payment['payment_method_name']; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Jumlah</div>
                                        <div class="fw-bold fs-5"><?php echo formatCurrency($payment['amount']); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Pembayaran</div>
                                        <div><?php echo date('d M Y H:i', strtotime($payment['created_at'])); ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Diperbarui</div>
                                        <div><?php echo date('d M Y H:i', strtotime($payment['updated_at'])); ?></div>
                                    </div>
                                    <?php if (!empty($payment['notes'])): ?>
                                    <div class="info-item">
                                        <div class="info-label">Catatan</div>
                                        <div><?php echo $payment['notes']; ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Reservasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Paket</div>
                                        <div><?php echo $payment['package_name']; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Kunjungan</div>
                                        <div><?php echo date('d M Y', strtotime($payment['visit_date'])); ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Jumlah Pengunjung</div>
                                        <div><?php echo $payment['num_visitors']; ?> orang</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Total Harga</div>
                                        <div><?php echo formatCurrency($payment['reservation_price']); ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Status Reservasi</div>
                                        <?php 
                                        $res_status_class = '';
                                        switch ($payment['reservation_status']) {
                                            case 'pending':
                                                $res_status_class = 'bg-warning';
                                                $res_status_text = 'Menunggu Pembayaran';
                                                break;
                                            case 'confirmed':
                                                $res_status_class = 'bg-success';
                                                $res_status_text = 'Terkonfirmasi';
                                                break;
                                            case 'cancelled':
                                                $res_status_class = 'bg-danger';
                                                $res_status_text = 'Dibatalkan';
                                                break;
                                            case 'completed':
                                                $res_status_class = 'bg-info';
                                                $res_status_text = 'Selesai';
                                                break;
                                            default:
                                                $res_status_class = 'bg-secondary';
                                                $res_status_text = $payment['reservation_status'];
                                        }
                                        ?>
                                        <div><span class="badge <?php echo $res_status_class; ?>"><?php echo $res_status_text; ?></span></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Tanggal Reservasi</div>
                                        <div><?php echo date('d M Y H:i', strtotime($payment['reservation_date'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($payment['notes'])): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="info-item">
                                        <div class="info-label">Catatan Reservasi</div>
                                        <div><?php echo $payment['notes']; ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Nama</div>
                                        <div><?php echo $payment['user_name']; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">WhatsApp</div>
                                        <div><?php echo $payment['whatsapp']; ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label">Email</div>
                                        <div><?php echo !empty($payment['email']) ? $payment['email'] : '-'; ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">ID Pengguna</div>
                                        <div>#<?php echo $payment['user_id']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Bukti Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($payment['proof_of_payment'])): ?>
                                <img src="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" class="payment-proof img-fluid mb-3" alt="Bukti Pembayaran" data-bs-toggle="modal" data-bs-target="#proofModal">
                                <div class="d-grid">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proofModal">
                                        <i class="fas fa-search-plus me-2"></i> Lihat Bukti Pembayaran
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Tidak ada bukti pembayaran</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($payment['status'] == 'pending'): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Tindakan</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $payment_id; ?>" method="post">
                                <div class="d-grid mb-3">
                                    <button type="submit" name="action" value="approve" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?')">
                                        <i class="fas fa-check me-2"></i> Setujui Pembayaran
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Catatan Penolakan (opsional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Masukkan alasan penolakan"></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                                        <i class="fas fa-times me-2"></i> Tolak Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tindakan Lainnya</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="reservation-detail.php?id=<?php echo $payment['reservation_id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-check me-2"></i> Lihat Detail Reservasi
                                </a>
                                <a href="user-detail.php?id=<?php echo $payment['user_id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-user me-2"></i> Lihat Detail Pengguna
                                </a>
                                <a href="payments.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i> Kembali ke Daftar Pembayaran
                                </a>
                            </div>
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
                    <?php if (!empty($payment['proof_of_payment'])): ?>
                        <img src="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" class="img-fluid" alt="Bukti Pembayaran">
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="mb-0">Tidak ada bukti pembayaran</p>
                        </div>
                    <?php endif; ?>
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
        });
    </script>
</body>

</html>
