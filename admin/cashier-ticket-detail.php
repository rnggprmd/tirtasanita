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

// Check if ticket ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID tiket tidak valid.', 'alert alert-danger');
    redirect("cashier-dashboard.php");
}

$ticket_id = $_GET['id'];

// Get ticket details
$sql = "SELECT r.*, p.name as package_name, u.name as user_name, u.whatsapp, u.email,
        pm.name as payment_method
        FROM reservations r
        JOIN packages p ON r.package_id = p.id
        JOIN users u ON r.user_id = u.id
        LEFT JOIN payments pay ON r.id = pay.reservation_id
        LEFT JOIN payment_methods pm ON pay.payment_method_id = pm.id
        WHERE r.id = :id LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $ticket_id);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    setFlashMessage('message', 'Tiket tidak ditemukan.', 'alert alert-danger');
    redirect("cashier-dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Tiket - Kasir</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="../img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('admin-style.css');
    </style>
</head>

<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar('cashier-ticket-detail.php'); ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="cashier-dashboard.php">Kasir</a>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-outline-primary" href="cashier-dashboard.php">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1>Detail Tiket</h1>
                    <small class="text-muted">Tiket Instant - <?php echo date('d M Y H:i', strtotime($ticket['created_at'])); ?></small>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Ticket Box -->
                    <div class="ticket-box">
                        <div class="ticket-header">
                            <div class="ticket-number">#<?php echo str_pad($ticket['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <p class="text-muted mb-0">TIKET MASUK</p>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Nama Pengunjung:</span>
                            <span class="info-value"><strong><?php echo $ticket['user_name']; ?></strong></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Paket Wisata:</span>
                            <span class="info-value"><strong><?php echo $ticket['package_name']; ?></strong></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Jumlah Tiket:</span>
                            <span class="info-value"><strong><?php echo $ticket['num_visitors']; ?> Tiket</strong></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Tanggal Kunjungan:</span>
                            <span class="info-value"><strong><?php echo date('d M Y', strtotime($ticket['visit_date'])); ?></strong></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Harga per Tiket:</span>
                            <span class="info-value"><strong><?php echo formatCurrency($ticket['is_weekday'] ? ($ticket['total_price'] / $ticket['num_visitors']) : ($ticket['total_price'] / $ticket['num_visitors'])); ?></strong></span>
                        </div>

                        <div class="info-row" style="border-bottom: 2px solid var(--primary-color); padding: 15px 0;">
                            <span class="info-label" style="font-size: 1.1em;">Total Harga:</span>
                            <span class="info-value" style="font-size: 1.3em; color: var(--primary-color);"><strong><?php echo formatCurrency($ticket['total_price']); ?></strong></span>
                        </div>

                        <div class="info-row mt-3">
                            <span class="info-label">Status Pembayaran:</span>
                            <span class="info-value"><span class="badge badge-success">✓ LUNAS</span></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Metode Pembayaran:</span>
                            <span class="info-value"><strong><?php echo $ticket['payment_method'] ?? 'Cash'; ?></strong></span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">No. WhatsApp:</span>
                            <span class="info-value"><strong><?php echo $ticket['whatsapp']; ?></strong></span>
                        </div>
                    </div>

                    <?php if (!empty($ticket['notes'])): ?>
                    <div class="card">
                        <div class="card-body">
                            <h6>Catatan:</h6>
                            <p class="mb-0"><?php echo nl2br($ticket['notes']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row gap-2">
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i> Cetak Tiket
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="send-ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-success w-100">
                                        <i class="fas fa-whatsapp me-2"></i> Kirim via WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                        </div>
                        <div class="card-body">
                            <h6>Langkah Selanjutnya:</h6>
                            <ol class="small">
                                <li>Cetak tiket untuk diberikan ke pengunjung</li>
                                <li>Atau kirim tiket via WhatsApp</li>
                                <li>Pengunjung dapat langsung masuk dengan tiket</li>
                            </ol>

                            <hr>

                            <h6>Tips:</h6>
                            <ul class="small">
                                <li>✓ Simpan nomor tiket untuk referensi</li>
                                <li>✓ Cetak berupa kertas atau digital</li>
                                <li>✓ Setiap tiket adalah 1 orang</li>
                                <li>✓ Pembayaran sudah lunas</li>
                            </ul>

                            <hr>

                            <div class="alert alert-info small">
                                <i class="fas fa-check-circle me-2"></i>
                                Tiket berhasil dijual dan pembayaran telah diterima!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });
    </script>
</body>

</html>
