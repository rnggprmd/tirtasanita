<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("login.php");
}

$user_id = $_SESSION['user_id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get user's reservations
$sql = "SELECT r.*, p.name as package_name, pc.name as category_name, 
        p.price_weekday, p.price_weekend,
        (SELECT proof_of_payment FROM payments WHERE reservation_id = r.id ORDER BY created_at DESC LIMIT 1) as payment_proof,
        CASE 
            WHEN r.status = 'pending' AND (SELECT proof_of_payment FROM payments WHERE reservation_id = r.id AND proof_of_payment IS NOT NULL LIMIT 1) IS NOT NULL THEN 'Menunggu Konfirmasi'
            WHEN r.status = 'pending' THEN 'Menunggu Pembayaran'
            WHEN r.status = 'confirmed' THEN 'Dikonfirmasi'
            WHEN r.status = 'completed' THEN 'Selesai'
            WHEN r.status = 'cancelled' THEN 'Dibatalkan'
            ELSE r.status
        END as status_text,
        CASE 
            WHEN r.status = 'pending' AND (SELECT proof_of_payment FROM payments WHERE reservation_id = r.id AND proof_of_payment IS NOT NULL LIMIT 1) IS NOT NULL THEN 'info'
            WHEN r.status = 'pending' THEN 'warning'
            WHEN r.status = 'confirmed' THEN 'success'
            WHEN r.status = 'completed' THEN 'info'
            WHEN r.status = 'cancelled' THEN 'danger'
            ELSE 'secondary'
        END as status_class
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        WHERE r.user_id = :user_id 
        ORDER BY r.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Tiket Saya - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Tiket, Reservasi" name="keywords" />
    <meta content="Daftar tiket reservasi di Taman Kopses Ciseeng" name="description" />

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet" />

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
        .round {
            border-radius: 10px;
        }
        .ticket-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }
        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .ticket-tear {
            position: relative;
            height: 10px;
            background-color: #f8f9fa;
        }
        .ticket-tear-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background-image: radial-gradient(circle at 10px -5px, transparent 12px, #f8f9fa 13px);
            background-size: 20px 20px;
            background-position: -10px 0;
        }
        .ticket-status {
            z-index: 1;
        }
        .bg-primary-light {
            background-color: rgba(46, 184, 114, 0.1);
        }
        .ticket-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary);
        }
        .ticket-actions {
            border-top: 1px dashed #dee2e6;
            padding-top: 1rem;
        }
        .ticket-qr {
            border: 1px solid #dee2e6;
            padding: 5px;
            background-color: #fff;
            border-radius: 5px;
        }
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
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
                    <li class="breadcrumb-item text-white active" aria-current="page">Tiket Saya</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Tickets Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">Tiket Saya</p>
                    <h1 class="display-5 mb-3">Daftar Reservasi & Tiket</h1>
                    <p class="mb-4">Berikut adalah daftar reservasi dan tiket yang telah Anda buat. Anda dapat melihat detail, mencetak tiket, atau melakukan pembayaran.</p>
                </div>
            </div>
            
            <?php displayFlashMessage(); ?>
            
            <?php if (empty($tickets)): ?>
                <div class="bg-white shadow-sm round p-5 text-center">
                    <div class="py-4">
                        <i class="fas fa-ticket-alt fa-5x text-primary mb-4"></i>
                        <h3 class="mb-3">Belum Ada Tiket</h3>
                        <p class="text-muted mb-4">Anda belum memiliki reservasi atau tiket. Silakan buat reservasi baru untuk menikmati fasilitas di Taman Kopses Ciseeng.</p>
                        <a href="reservation.php" class="btn btn-primary py-3 px-5 rounded-pill">
                            <i class="fas fa-plus-circle me-2"></i> Buat Reservasi Sekarang
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($tickets as $ticket): ?>
                        <?php 
                        $icon = 'clock';
                        if ($ticket['status'] == 'confirmed') $icon = 'check-circle';
                        elseif ($ticket['status'] == 'completed') $icon = 'check-double';
                        elseif ($ticket['status'] == 'cancelled') $icon = 'times-circle';
                        ?>
                        <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="bg-white shadow-sm round overflow-hidden ticket-card">
                                <!-- Ticket Header -->
                                <div class="position-relative">
                                    <div class="bg-primary p-4 text-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 text-white"><i class="fas fa-ticket-alt me-2"></i> Tiket #<?php echo $ticket['id']; ?></h4>
                                            
                                        </div>
                                        <div class="ticket-status">
                                            <span class="badge bg-<?php echo $ticket['status_class']; ?> py-2 px-3">
                                                <i class="fas fa-<?php echo $icon; ?> me-1"></i> <?php echo $ticket['status_text']; ?>
                                            </span>
                                            <p class="mb-0 small"><?php echo date('d M Y H:i', strtotime($ticket['created_at'])); ?></p>
                                        </div>
                                    </div>
                                    <!-- Ticket Tear Edge -->
                                    <div class="ticket-tear">
                                        <div class="ticket-tear-pattern"></div>
                                    </div>
                                </div>
                                
                                <!-- Ticket Body -->
                                <div class="p-4">
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 40px; height: 40px">
                                                    <i class="fa fa-box text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Paket</h6>
                                                    <p class="mb-0 fw-bold"><?php echo $ticket['package_name']; ?></p>
                                                    <p class="mb-0 small text-muted"><?php echo $ticket['category_name']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 40px; height: 40px">
                                                    <i class="fa fa-calendar-check text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Tanggal Kunjungan</h6>
                                                    <p class="mb-0 fw-bold"><?php echo date('d M Y', strtotime($ticket['visit_date'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 40px; height: 40px">
                                                    <i class="fa fa-users text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Jumlah Pengunjung</h6>
                                                    <p class="mb-0 fw-bold"><?php echo $ticket['num_visitors']; ?> orang</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 40px; height: 40px">
                                                    <i class="fa fa-money-bill-wave text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Total Harga</h6>
                                                    <p class="mb-0 fw-bold">Rp <?php echo number_format($ticket['total_price'], 0, ',', '.'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Ticket Actions -->
                                    <div class="ticket-actions mt-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <?php if ($ticket['status'] == 'confirmed'): ?>
                                                <div class="ticket-qr text-center mb-2">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=TICKET-<?php echo $ticket['id']; ?>" 
                                                        alt="QR Code" class="img-fluid" style="max-width: 100px;">
                                                </div>
                                                <p class="text-center small text-muted mb-0">Scan untuk validasi</p>
                                                <?php else: ?>
                                                <div class="ticket-price">
                                                    <?php 
                                                    $price = $ticket['is_weekday'] ? 
                                                        number_format($ticket['price_weekday'] * $ticket['num_visitors'], 0, ',', '.') : 
                                                        number_format($ticket['price_weekend'] * $ticket['num_visitors'], 0, ',', '.');
                                                    ?>
                                                    <span>Rp <?php echo $price; ?></span>
                                                    <div class="small text-muted">Total untuk <?php echo $ticket['num_visitors']; ?> pengunjung</div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex flex-column gap-2">
                                                    <a href="reservation-detail.php?id=<?php echo $ticket['id']; ?>" class="btn btn-outline-primary rounded-pill">
                                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                                    </a>
                                                    
                                                    <?php if ($ticket['status'] == 'pending' && empty($ticket['payment_proof'])): ?>
                                                        <a href="payment.php?id=<?php echo $ticket['id']; ?>" class="btn btn-primary rounded-pill">
                                                            <i class="fas fa-credit-card me-1"></i> Bayar Sekarang
                                                        </a>
                                                    <?php elseif ($ticket['status'] == 'confirmed'): ?>
                                                        <a href="print-ticket.php?id=<?php echo $ticket['id']; ?>" target="_blank" class="btn btn-success rounded-pill">
                                                            <i class="fas fa-print me-1"></i> Cetak Tiket
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Tickets End -->

    <!-- Footer Start -->
    <?php include_once '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
