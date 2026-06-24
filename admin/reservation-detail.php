<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID reservasi tidak valid.', 'alert alert-danger');
    redirect("reservations.php");
}

$reservation_id = $_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get reservation details with package facilities
$sql = "SELECT r.*, p.name as package_name, p.description as package_description, 
        p.price_weekday, p.price_weekend, pc.name as category_name, u.name as user_name, u.whatsapp, u.email,
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
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan.', 'alert alert-danger');
    redirect("reservations.php");
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

// Process status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $status = '';
    $message = '';
    
    switch ($action) {
        case 'confirm':
            $status = 'confirmed';
            $message = 'Reservasi berhasil dikonfirmasi.';
            break;
        case 'complete':
            $status = 'completed';
            $message = 'Reservasi berhasil diselesaikan.';
            break;
        case 'cancel':
            $status = 'cancelled';
            $message = 'Reservasi berhasil dibatalkan.';
            break;
        default:
            setFlashMessage('message', 'Aksi tidak valid.', 'alert alert-danger');
            redirect("reservation-detail.php?id=" . $reservation_id);
    }
    
    $sql = "UPDATE reservations SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $reservation_id);
    
    if ($stmt->execute()) {
        setFlashMessage('message', $message, 'alert alert-success');
    } else {
        setFlashMessage('message', 'Terjadi kesalahan saat memperbarui status reservasi.', 'alert alert-danger');
    }
    
    redirect("reservation-detail.php?id=" . $reservation_id);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Reservasi - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Detail Reservasi" name="keywords">
    <meta content="Admin panel untuk melihat detail reservasi di Taman Kopses Ciseeng" name="description">

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
    
    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet" />
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet" />
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

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
        }

        .sidebar .sidebar-menu .nav-link i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding: 20px;
            transition: all 0.3s;
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
        }

        .card {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table th {
            font-weight: 600;
        }

        .badge {
            padding: 0.5em 0.75em;
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
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
                        <h1 class="mb-0">Detail Reservasi #<?php echo $reservation['id']; ?></h1>
                        <a href="reservations.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Informasi Reservasi</h5>
                            <span class="badge bg-<?php echo $reservation['status_class']; ?>"><?php echo $reservation['status_text']; ?></span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">ID Reservasi</th>
                                        <td width="70%">#<?php echo $reservation['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td><span class="badge bg-<?php echo $reservation['status_class']; ?>"><?php echo $reservation['status_text']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Reservasi</th>
                                        <td><?php echo date('d M Y H:i', strtotime($reservation['created_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Kunjungan</th>
                                        <td><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Hari</th>
                                        <td><?php echo $reservation['is_weekday'] ? 'Weekday' : 'Weekend'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Jumlah Pengunjung</th>
                                        <td><?php echo $reservation['num_visitors']; ?> orang</td>
                                    </tr>
                                    <tr>
                                        <th>Harga per Orang</th>
                                        <td>Rp <?php echo number_format($reservation['is_weekday'] ? $reservation['price_weekday'] : $reservation['price_weekend'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Harga</th>
                                        <td class="fw-bold">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Paket</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Kategori Paket</th>
                                        <td width="70%"><?php echo $reservation['category_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Paket</th>
                                        <td><?php echo $reservation['package_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td><?php echo nl2br($reservation['package_description']); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <?php if (!empty($facilities)): ?>
                                <h6 class="mt-3 mb-2">Fasilitas</h6>
                                <div class="row g-3">
                                    <?php foreach ($facilities as $facility): ?>
                                        <div class="col-md-4 col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="<?php echo $facility['icon']; ?> text-primary me-2"></i>
                                                <span><?php echo $facility['name']; ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($payment): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Metode Pembayaran</th>
                                            <td width="70%"><?php echo $payment['payment_method_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah</th>
                                            <td>Rp <?php echo number_format($payment['amount'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status Pembayaran</th>
                                            <td>
                                                <?php if ($payment['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                                <?php elseif ($payment['status'] == 'completed'): ?>
                                                    <span class="badge bg-success">Selesai</span>
                                                <?php elseif ($payment['status'] == 'failed'): ?>
                                                    <span class="badge bg-danger">Gagal</span>
                                                <?php elseif ($payment['status'] == 'refunded'): ?>
                                                    <span class="badge bg-info">Dikembalikan</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Pembayaran</th>
                                            <td><?php echo date('d M Y H:i', strtotime($payment['created_at'])); ?></td>
                                        </tr>
                                        <?php if (!empty($payment['proof_of_payment'])): ?>
                                            <tr>
                                                <th>Bukti Pembayaran</th>
                                                <td>
                                                    <div class="mb-2">
                                                        <a href="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" 
                                                        data-lightbox="proof-of-payment" 
                                                        data-title="Bukti Pembayaran Reservasi #<?php echo $reservation['id']; ?>" 
                                                        class="btn btn-primary">
                                                            <i class="fas fa-image me-1"></i> Lihat Bukti Pembayaran
                                                        </a>
                                                    </div>
                                                    <div class="payment-proof-thumbnail mt-2">
                                                        <a href="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" 
                                                        data-lightbox="proof-of-payment-thumb" 
                                                        data-title="Bukti Pembayaran Reservasi #<?php echo $reservation['id']; ?>">
                                                            <img src="../uploads/payments/<?php echo $payment['proof_of_payment']; ?>" 
                                                                alt="Bukti Pembayaran" 
                                                                class="img-thumbnail" 
                                                                style="max-height: 150px; max-width: 100%;">
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pelanggan</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Nama</th>
                                        <td width="70%"><?php echo $reservation['user_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>WhatsApp</th>
                                        <td>
                                            <?php
                                            // Format WhatsApp number
                                            $whatsapp = preg_replace('/[^0-9]/', '', $reservation['whatsapp']);
                                            if (substr($whatsapp, 0, 1) == '0') {
                                                $whatsapp = '62' . substr($whatsapp, 1);
                                            }
                                            if (substr($whatsapp, 0, 2) != '62') {
                                                $whatsapp = '62' . $whatsapp;
                                            }
                                            ?>
                                            <a href="https://wa.me/<?php echo $whatsapp; ?>" target="_blank" class="text-decoration-none">
                                                <?php echo $reservation['whatsapp']; ?> <i class="fab fa-whatsapp text-success"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php if (!empty($reservation['email'])): ?>
                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                <a href="mailto:<?php echo $reservation['email']; ?>" class="text-decoration-none">
                                                    <?php echo $reservation['email']; ?> <i class="fas fa-envelope text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reservation_id); ?>" method="post" class="d-grid gap-2">
                                <?php if ($reservation['status'] == 'pending'): ?>
                                    <button type="submit" name="action" value="confirm" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i> Konfirmasi Reservasi
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn btn-danger">
                                        <i class="fas fa-times me-2"></i> Batalkan Reservasi
                                    </button>
                                <?php elseif ($reservation['status'] == 'confirmed'): ?>
                                    <button type="submit" name="action" value="complete" class="btn btn-info">
                                        <i class="fas fa-check-double me-2"></i> Selesaikan Reservasi
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn btn-danger">
                                        <i class="fas fa-times me-2"></i> Batalkan Reservasi
                                    </button>
                                    <a href="send-ticket.php?id=<?php echo $reservation_id; ?>" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i> Kirim Tiket
                                    </a>
                                <?php elseif ($reservation['status'] == 'cancelled'): ?>
                                    <button type="submit" name="action" value="confirm" class="btn btn-success">
                                        <i class="fas fa-redo me-2"></i> Aktifkan Kembali
                                    </button>
                                <?php endif; ?>
                                <a href="print-ticket.php?id=<?php echo $reservation_id; ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-print me-2"></i> Cetak Tiket
                                </a>
                            </form>
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
    <script src="../lib/lightbox/js/lightbox.min.js"></script>

    <!-- Admin Javascript -->
    <script>
        // Sidebar Toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Format WhatsApp number for links
        function formatWhatsAppNumber(number) {
            // Remove any non-numeric characters
            number = number.replace(/\D/g, '');
            
            // Check if number starts with '0', replace with '62'
            if (number.startsWith('0')) {
                number = '62' + number.substring(1);
            }
            
            // Check if number doesn't start with '62', add it
            if (!number.startsWith('62')) {
                number = '62' + number;
            }
            
            return number;
        }
    </script>
</body>

</html>
