<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("login.php");
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get user's reservations
$database = new Database();
$db = $database->getConnection();

$sql = "SELECT r.*, p.name as package_name, p.price_weekday, p.price_weekend, pc.name as category_name 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        WHERE r.user_id = :user_id 
        ORDER BY r.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Dashboard - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Dashboard, Reservasi" name="keywords" />
    <meta content="Dashboard pengguna Taman Kopses Ciseeng" name="description" />

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet" />
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet" />
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet" />
    
    <style>
        .profile-header {
            padding: 1.5rem;
            border-radius: 0.5rem;
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .bg-primary-light {
            background-color: rgba(77, 195, 135, 0.1);
        }
        
        .round {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <?php include_once '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include_once '../includes/navbar.php'; ?>
    <!-- Navbar End -->

   <!-- Page Header Start -->
   <div class="container-fluid wow bg-primary fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Dashboard Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-white shadow-sm round p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-th-large me-2 text-primary"></i>Menu Utama</h3>
                        </div>
                        <div class="d-grid gap-3">
                            <a href="reservation.php" class="btn btn-primary py-3 rounded-pill">
                                <i class="fas fa-ticket-alt me-2"></i>Buat Reservasi Baru
                            </a>
                            <a href="my-tickets.php" class="btn btn-outline-primary py-3 rounded-pill">
                                <i class="fas fa-qrcode me-2"></i>Tiket Saya
                            </a>
                            <a href="profile.php" class="btn btn-outline-primary py-3 rounded-pill">
                                <i class="fas fa-user-cog me-2"></i>Pengaturan Akun
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-white shadow-sm round p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Reservasi</h3>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <?php if (empty($reservations)): ?>
                            <div class="text-center py-5 bg-primary-light round">
                                <i class="fas fa-ticket-alt fa-4x text-primary mb-4"></i>
                                <h4 class="mb-3">Belum ada reservasi</h4>
                                <p class="text-muted mb-4">Anda belum memiliki reservasi. Silakan buat reservasi baru untuk menikmati fasilitas di Taman Kopses Ciseeng.</p>
                                <a href="reservation.php" class="btn btn-primary rounded-pill px-4 py-2">
                                    <i class="fas fa-plus-circle me-2"></i>Buat Reservasi Sekarang
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead>
                                        <tr class="bg-primary-light">
                                            <th class="py-3">ID</th>
                                            <th class="py-3">Paket</th>
                                            <th class="py-3">Tanggal</th>
                                            <th class="py-3">Jumlah</th>
                                            <th class="py-3">Total</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservations as $reservation): ?>
                                            <tr>
                                                <td><strong>#<?php echo $reservation['id']; ?></strong></td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold"><?php echo $reservation['package_name']; ?></span>
                                                        <small class="text-muted"><?php echo $reservation['category_name']; ?></small>
                                                    </div>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></td>
                                                <td><?php echo $reservation['num_visitors']; ?> orang</td>
                                                <td><strong><?php echo formatCurrency($reservation['total_price']); ?></strong></td>
                                                <td>
                                                    <?php 
                                                    $status_class = '';
                                                    $status_icon = '';
                                                    switch ($reservation['status']) {
                                                        case 'pending':
                                                            $status_class = 'badge bg-warning';
                                                            $status_text = 'Menunggu Pembayaran';
                                                            $status_icon = 'clock';
                                                            break;
                                                        case 'confirmed':
                                                            $status_class = 'badge bg-success';
                                                            $status_text = 'Terkonfirmasi';
                                                            $status_icon = 'check-circle';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'badge bg-danger';
                                                            $status_text = 'Dibatalkan';
                                                            $status_icon = 'times-circle';
                                                            break;
                                                        case 'completed':
                                                            $status_class = 'badge bg-info';
                                                            $status_text = 'Selesai';
                                                            $status_icon = 'check-double';
                                                            break;
                                                        default:
                                                            $status_class = 'badge bg-secondary';
                                                            $status_text = $reservation['status'];
                                                            $status_icon = 'info-circle';
                                                    }
                                                    ?>
                                                    <span class="<?php echo $status_class; ?> py-2 px-3">
                                                        <i class="fas fa-<?php echo $status_icon; ?> me-1"></i> <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="reservation-detail.php?id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-primary rounded-pill">
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($reservations) > 5): ?>
                                <div class="text-center mt-4">
                                    <a href="my-tickets.php" class="btn btn-outline-primary rounded-pill">
                                        <i class="fas fa-list me-2"></i>Lihat Semua Reservasi
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard End -->

    <!-- Footer Start -->
    <?php include_once '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>
