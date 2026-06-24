<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("login.php");
}

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID reservasi tidak valid.', 'alert alert-danger');
    redirect("my-tickets.php");
}

$reservation_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get reservation details with package facilities
$sql = "SELECT r.*, p.name as package_name, p.description as package_description, 
        p.price_weekday, p.price_weekend, pc.name as category_name,
        CASE 
            WHEN r.status = 'pending' THEN 'Menunggu Konfirmasi'
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
        END as status_class
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        WHERE r.id = :id AND r.user_id = :user_id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan.', 'alert alert-danger');
    redirect("my-tickets.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Get package facilities
$sql = "SELECT f.name, f.icon 
        FROM package_facilities pf 
        JOIN facilities f ON pf.facility_id = f.id 
        WHERE pf.package_id = :package_id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $reservation['package_id']);
$stmt->execute();
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment details if any
$sql = "SELECT p.*, pm.name as payment_method_name 
        FROM payments p 
        JOIN payment_methods pm ON p.payment_method_id = pm.id 
        WHERE p.reservation_id = :reservation_id 
        ORDER BY p.created_at DESC 
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':reservation_id', $reservation_id);
$stmt->execute();
$payment = $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Detail Reservasi - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Detail Reservasi, Tiket" name="keywords" />
    <meta content="Detail reservasi di Taman Kopses Ciseeng" name="description" />

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
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
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
                    <li class="breadcrumb-item text-white active" aria-current="page">Detail Reservasi</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Reservation Detail Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 wow fadeInUp mb-2" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">Detail Reservasi</p>
                    <h1 class="display-5 mb-4">Reservasi #<?php echo $reservation['id']; ?></h1>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="bg-white shadow-sm round h-100 p-4 mb-4 round">
                                <h4 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Status Reservasi
                                </h4>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-<?php echo $reservation['status_class']; ?> rounded-circle"
                                        style="width: 50px; height: 50px">
                                        <?php
                                        $icon = 'clock';
                                        if ($reservation['status'] == 'confirmed')
                                            $icon = 'check-circle';
                                        elseif ($reservation['status'] == 'completed')
                                            $icon = 'check-double';
                                        elseif ($reservation['status'] == 'cancelled')
                                            $icon = 'times-circle';
                                        ?>
                                        <i class="fa fa-<?php echo $icon; ?> text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-0"><?php echo $reservation['status_text']; ?></h5>
                                        <p class="text-muted mb-0">Terakhir diperbarui:
                                            <?php echo date('d M Y H:i', strtotime($reservation['updated_at'] ?? $reservation['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-4 mb-3">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle"
                                        style="width: 40px; height: 40px">
                                        <i class="fa fa-calendar-check text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Tanggal Kunjungan</h6>
                                        <p class="mb-0">
                                            <?php echo date('d M Y', strtotime($reservation['visit_date'])); ?>
                                            (<?php echo $reservation['is_weekday'] ? 'Weekday' : 'Weekend'; ?>)
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-4 mb-3">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle"
                                        style="width: 40px; height: 40px">
                                        <i class="fa fa-users text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Jumlah Pengunjung</h6>
                                        <p class="mb-0"><?php echo $reservation['num_visitors']; ?> orang</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-4">
                                    <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle"
                                        style="width: 40px; height: 40px">
                                        <i class="fa fa-money-bill-wave text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Total Pembayaran</h6>
                                        <p class="mb-0 fw-bold">Rp
                                            <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="bg-white shadow-sm h-100 round p-4 mb-4">
                                <h4 class="fw-bold mb-3"><i class="fas fa-box-open me-2 text-primary"></i>Detail Paket
                                </h4>
                                <div class="mb-3">
                                    <h5 class="mb-1"><?php echo $reservation['package_name']; ?></h5>
                                    <p class="text-muted"><?php echo $reservation['category_name']; ?></p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="mb-2">Deskripsi Paket</h6>
                                    <p class="mb-0"><?php echo nl2br($reservation['package_description']); ?></p>
                                </div>

                                <?php if (!empty($facilities)): ?>
                                    <div class="mt-4">
                                        <h6 class="mb-3">Fasilitas</h6>
                                        <div class="row g-3">
                                            <?php foreach ($facilities as $facility): ?>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center">
                                                        <i class="<?php echo $facility['icon']; ?> text-primary me-2"></i>
                                                        <span><?php echo $facility['name']; ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php displayFlashMessage(); ?>
                </div>

                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <?php if ($payment): ?>
                        <div class="bg-white shadow-sm round p-4 mb-4 h-100">
                            <h4 class="fw-bold mb-3"><i class="fas fa-credit-card me-2 text-primary"></i>Detail Pembayaran
                            </h4>

                            <div class="d-flex align-items-center mb-3">
                                <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle"
                                    style="width: 40px; height: 40px">
                                    <?php
                                    $paymentIcon = 'money-bill';
                                    if (strpos(strtolower($payment['payment_method_name']), 'bank') !== false)
                                        $paymentIcon = 'university';
                                    elseif (strpos(strtolower($payment['payment_method_name']), 'transfer') !== false)
                                        $paymentIcon = 'exchange-alt';
                                    ?>
                                    <i class="fa fa-<?php echo $paymentIcon; ?> text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0"><?php echo $payment['payment_method_name']; ?></h5>
                                    <p class="text-muted mb-0">Dibayar pada:
                                        <?php echo date('d M Y H:i', strtotime($payment['created_at'])); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="bg-primary text-white p-3 round mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Total Pembayaran:</span>
                                    <span class="fw-bold fs-5">Rp
                                        <?php echo number_format($payment['amount'], 0, ',', '.'); ?></span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Status Pembayaran:</span>
                                <?php if ($payment['status'] == 'pending'): ?>
                                    <span class="badge bg-warning py-2 px-3"><i class="fas fa-clock me-1"></i> Menunggu
                                        Pembayaran</span>
                                <?php elseif ($payment['status'] == 'completed'): ?>
                                    <span class="badge bg-success py-2 px-3"><i class="fas fa-check-circle me-1"></i>
                                        Dikonfirmasi</span>
                                <?php elseif ($payment['status'] == 'failed'): ?>
                                    <span class="badge bg-danger py-2 px-3"><i class="fas fa-times-circle me-1"></i>
                                        Gagal</span>
                                <?php elseif ($payment['status'] == 'refunded'): ?>
                                    <span class="badge bg-danger py-2 px-3"><i class="fas fa-undo me-1"></i> Dikembalikan</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($payment['proof_of_payment'])): ?>
                                <div class="mt-4">
                                    <h6 class="mb-2">Bukti Pembayaran</h6>
                                    <div class="payment-proof-thumbnail mb-2">
                                        <a href="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>"
                                            data-lightbox="proof-of-payment"
                                            data-title="Bukti Pembayaran Reservasi #<?php echo $reservation['id']; ?>">
                                            <img src="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>"
                                                alt="Bukti Pembayaran" class="img-thumbnail"
                                                style="max-height: 150px; max-width: 100%;">
                                        </a>
                                    </div>
                                    <a href="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>"
                                        data-lightbox="proof-of-payment-btn"
                                        data-title="Bukti Pembayaran Reservasi #<?php echo $reservation['id']; ?>"
                                        class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-search-plus me-1"></i> Lihat Bukti Pembayaran
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-white shadow-sm round p-4 p-lg-5 mb-4">
                        <h4 class="fw-bold mb-4"><i class="fas fa-tasks me-2 text-primary"></i>Aksi Reservasi</h4>

                        <?php if ($reservation['status'] == 'pending'): ?>
                            <?php if ($payment && !empty($payment['proof_of_payment'])): ?>
                                <div class="alert alert-info mb-4 round">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-info-circle fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h5 class="alert-heading mb-2">Menunggu Konfirmasi</h5>
                                            <p class="mb-0">Bukti pembayaran Anda telah diunggah dan sedang menunggu konfirmasi
                                                admin. Kami akan memproses pembayaran Anda segera.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-4 round">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="alert-heading mb-2">Menunggu Pembayaran</h5>
                                            <p class="mb-0">Reservasi Anda sedang menunggu pembayaran. Silakan lakukan
                                                pembayaran untuk mengkonfirmasi reservasi Anda.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid mb-4">
                                    <a href="payment.php?id=<?php echo $reservation['id']; ?>"
                                        class="btn btn-primary py-3 rounded-pill">
                                        <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <a href="reservation.php?edit=<?php echo $reservation['id']; ?>"
                                        class="btn btn-info text-white w-100 py-3 rounded-pill">
                                        <i class="fas fa-edit me-2"></i> Edit Reservasi
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger w-100 py-3 rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#cancelModal">
                                        <i class="fas fa-times-circle me-2"></i> Batalkan Reservasi
                                    </button>
                                </div>
                            </div>

                        <?php elseif ($reservation['status'] == 'confirmed'): ?>
                            <div class="alert alert-success mb-4 round">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading mb-2">Reservasi Dikonfirmasi</h5>
                                        <p class="mb-0">Reservasi Anda telah dikonfirmasi. Anda dapat mencetak tiket Anda
                                            sekarang untuk dibawa saat kunjungan.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <a href="print-ticket.php?id=<?php echo $reservation['id']; ?>"
                                    class="btn btn-primary py-3 rounded-pill" target="_blank">
                                    <i class="fas fa-print me-2"></i> Cetak Tiket
                                </a>
                            </div>

                        <?php elseif ($reservation['status'] == 'cancelled'): ?>
                            <div class="alert alert-danger mb-4 round">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading mb-2">Reservasi Dibatalkan</h5>
                                        <p class="mb-0">Reservasi ini telah dibatalkan. Jika Anda ingin membuat reservasi
                                            baru, silakan kunjungi halaman reservasi.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <a href="my-tickets.php" class="btn btn-outline-secondary rounded-pill py-3">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Tiket
                            </a>
                        </div>
                    </div>

                    <div class="bg-primary-light p-4 round">
                        <h4 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h4>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle"
                                style="width: 40px; height: 40px">
                                <i class="fa fa-clock text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Jam Operasional</h6>
                                <p class="mb-0">Senin - Minggu: 08.00 - 17.00 WIB</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle"
                                style="width: 40px; height: 40px">
                                <i class="fa fa-map-marker-alt text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Lokasi</h6>
                                <p class="mb-0">Jl. Raya Ciseeng, Ciseeng, Bogor, Jawa Barat</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-white rounded-circle"
                                style="width: 40px; height: 40px">
                                <i class="fa fa-phone-alt text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Kontak</h6>
                                <p class="mb-0">+62 812-3456-7890</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reservation Detail End -->

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

    <!-- Cancel Reservation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan reservasi ini?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Pembatalan tidak dapat dibatalkan dan reservasi
                        yang sudah dibatalkan tidak dapat dipulihkan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="cancel-reservation.php?id=<?php echo $reservation['id']; ?>" class="btn btn-danger">Ya,
                        Batalkan Reservasi</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>