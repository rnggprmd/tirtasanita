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

// Get reservation details
$sql = "SELECT r.*, p.name as package_name, pc.name as category_name, u.name as user_name, u.whatsapp, u.email 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id AND r.status = 'confirmed'";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan atau belum dikonfirmasi.', 'alert alert-danger');
    redirect("reservations.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $whatsapp = isset($_POST['whatsapp']) ? trim($_POST['whatsapp']) : '';
    
    // Validate WhatsApp number
    if (empty($whatsapp)) {
        setFlashMessage('message', 'Nomor WhatsApp tidak boleh kosong.', 'alert alert-danger');
    } else {
        // Format WhatsApp number
        $whatsapp = formatWhatsAppNumber($whatsapp);
        
        // Get base URL from server information
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . $host;
        
        // Generate ticket URL
        $ticket_url = $base_url . "/tkc/user/print-ticket.php?id=" . $reservation['id'];
        
        // Generate notification message
        $message = "🎫 *NOTIFIKASI RESERVASI TAMAN KOPSES CISEENG* 🎫\n\n";
        $message .= "Halo " . $reservation['user_name'] . ",\n\n";
        $message .= "Reservasi Anda dengan nomor #" . $reservation['id'] . " telah DIKONFIRMASI.\n\n";
        $message .= "Detail Reservasi:\n";
        $message .= "- Paket: " . $reservation['package_name'] . "\n";
        $message .= "- Tanggal Kunjungan: " . date('d M Y', strtotime($reservation['visit_date'])) . "\n";
        $message .= "- Jumlah Pengunjung: " . $reservation['num_visitors'] . " orang\n\n";
        $message .= "Silakan cetak tiket Anda melalui link berikut:\n";
        $message .= $ticket_url . "\n\n";
        $message .= "Atau melalui halaman 'Tiket Saya' di website kami.\n\n";
        $message .= "Terima kasih telah memilih Taman Kopses Ciseeng! 🌿";
        
        // Encode message for URL
        $encoded_message = urlencode($message);
        
        // Set success message
        setFlashMessage('message', 'Notifikasi berhasil dikirim ke WhatsApp pelanggan.', 'alert alert-success');
        
        // Redirect to WhatsApp
        $whatsapp_url = "https://wa.me/" . $whatsapp . "?text=" . $encoded_message;
        header("Location: " . $whatsapp_url);
        exit;
    }
}

// Function to format WhatsApp number
function formatWhatsAppNumber($number) {
    // Remove any non-numeric characters
    $number = preg_replace('/[^0-9]/', '', $number);
    
    // Check if number starts with '0', replace with '62'
    if (substr($number, 0, 1) == '0') {
        $number = '62' . substr($number, 1);
    }
    
    // Check if number doesn't start with '62', add it
    if (substr($number, 0, 2) != '62') {
        $number = '62' . $number;
    }
    
    return $number;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kirim Tiket - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Kirim Tiket" name="keywords">
    <meta content="Admin panel untuk mengirim tiket di Taman Kopses Ciseeng" name="description">

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
                        <h1 class="mb-0">Kirim Tiket</h1>
                        <a href="reservation-detail.php?id=<?php echo $reservation_id; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Kirim E-Ticket Via WhatsApp</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Kirim e-ticket ke pelanggan melalui WhatsApp. Pastikan nomor WhatsApp yang dimasukkan aktif.
                            </div>

                            <div class="mb-4">
                                <div class="card border-primary mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Detail Tiket #<?php echo $reservation['id']; ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4 fw-bold">Nama Pelanggan:</div>
                                            <div class="col-md-8"><?php echo $reservation['user_name']; ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4 fw-bold">Kategori:</div>
                                            <div class="col-md-8"><?php echo $reservation['category_name']; ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4 fw-bold">Paket:</div>
                                            <div class="col-md-8"><?php echo $reservation['package_name']; ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4 fw-bold">Tanggal Kunjungan:</div>
                                            <div class="col-md-8"><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4 fw-bold">Jumlah Pengunjung:</div>
                                            <div class="col-md-8"><?php echo $reservation['num_visitors']; ?> orang</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 fw-bold">Total Harga:</div>
                                            <div class="col-md-8">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $reservation_id); ?>" method="post">
                                <div class="mb-4">
                                    <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                               placeholder="Contoh: 081234567890" 
                                               value="<?php echo $reservation['whatsapp']; ?>" required>
                                    </div>
                                    <div class="form-text">Masukkan nomor WhatsApp yang aktif untuk menerima e-ticket.</div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary py-3">
                                        <i class="fab fa-whatsapp me-2"></i> Kirim via WhatsApp
                                    </button>
                                    <a href="reservation-detail.php?id=<?php echo $reservation_id; ?>" class="btn btn-outline-secondary py-3">
                                        <i class="fas fa-arrow-left me-2"></i> Kembali
                                    </a>
                                </div>
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

    <!-- Admin Javascript -->
    <script>
        // Sidebar Toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>
