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

// Initialize variables
$site_name = '';
$site_description = '';
$contact_whatsapp = '';
$contact_instagram = '';
$contact_facebook = '';
$weekday_start_time = '';
$weekday_end_time = '';
$weekend_start_time = '';
$weekend_end_time = '';
$holiday_start_time = '';
$holiday_end_time = '';
$maintenance_mode = 0;
$max_reservations_per_day = 0;
$payment_bank_transfer = 0;
$payment_bank_account = '';
$payment_qris = 0;
$payment_qris_image = '';
$payment_cash = 0;
$payment_confirmation_required = 1;

// Get current settings
$sql = "SELECT * FROM settings";
$stmt = $db->prepare($sql);
$stmt->execute();
$settings = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Set values from database if they exist
if (isset($settings['site_name']))
    $site_name = $settings['site_name'];
if (isset($settings['site_description']))
    $site_description = $settings['site_description'];
if (isset($settings['contact_whatsapp']))
    $contact_whatsapp = $settings['contact_whatsapp'];
if (isset($settings['contact_instagram']))
    $contact_instagram = $settings['contact_instagram'];
if (isset($settings['contact_facebook']))
    $contact_facebook = $settings['contact_facebook'];
if (isset($settings['weekday_start_time']))
    $weekday_start_time = $settings['weekday_start_time'];
if (isset($settings['weekday_end_time']))
    $weekday_end_time = $settings['weekday_end_time'];
if (isset($settings['weekend_start_time']))
    $weekend_start_time = $settings['weekend_start_time'];
if (isset($settings['weekend_end_time']))
    $weekend_end_time = $settings['weekend_end_time'];
if (isset($settings['holiday_start_time']))
    $holiday_start_time = $settings['holiday_start_time'];
if (isset($settings['holiday_end_time']))
    $holiday_end_time = $settings['holiday_end_time'];
if (isset($settings['payment_bank_transfer']))
    $payment_bank_transfer = $settings['payment_bank_transfer'];
if (isset($settings['payment_bank_account']))
    $payment_bank_account = $settings['payment_bank_account'];
if (isset($settings['payment_qris']))
    $payment_qris = $settings['payment_qris'];
if (isset($settings['payment_qris_image']))
    $payment_qris_image = $settings['payment_qris_image'];
if (isset($settings['payment_cash']))
    $payment_cash = $settings['payment_cash'];
if (isset($settings['payment_confirmation_required']))
    $payment_confirmation_required = $settings['payment_confirmation_required'];
if (isset($settings['maintenance_mode']))
    $maintenance_mode = $settings['maintenance_mode'];
if (isset($settings['max_reservations_per_day']))
    $max_reservations_per_day = $settings['max_reservations_per_day'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $site_name = trim($_POST['site_name']);
    $site_description = trim($_POST['site_description']);
    $contact_whatsapp = trim($_POST['contact_whatsapp']);
    $contact_instagram = trim($_POST['contact_instagram']);
    $contact_facebook = trim($_POST['contact_facebook']);
    $weekday_start_time = trim($_POST['weekday_start_time']);
    $weekday_end_time = trim($_POST['weekday_end_time']);
    $weekend_start_time = trim($_POST['weekend_start_time']);
    $weekend_end_time = trim($_POST['weekend_end_time']);
    $holiday_start_time = trim($_POST['holiday_start_time']);
    $holiday_end_time = trim($_POST['holiday_end_time']);
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    $max_reservations_per_day = intval($_POST['max_reservations_per_day']);
    $payment_bank_transfer = isset($_POST['payment_bank_transfer']) ? 1 : 0;
    $payment_bank_account = trim($_POST['payment_bank_account']);
    $payment_qris = isset($_POST['payment_qris']) ? 1 : 0;
    $payment_qris_image = trim($_POST['payment_qris_image']);
    $payment_cash = isset($_POST['payment_cash']) ? 1 : 0;
    $payment_confirmation_required = isset($_POST['payment_confirmation_required']) ? 1 : 0;

    // Update or insert settings
    $settings_to_update = [
        'site_name' => $site_name,
        'site_description' => $site_description,
        'contact_whatsapp' => $contact_whatsapp,
        'contact_instagram' => $contact_instagram,
        'contact_facebook' => $contact_facebook,
        'weekday_start_time' => $weekday_start_time,
        'weekday_end_time' => $weekday_end_time,
        'weekend_start_time' => $weekend_start_time,
        'weekend_end_time' => $weekend_end_time,
        'holiday_start_time' => $holiday_start_time,
        'holiday_end_time' => $holiday_end_time,
        'maintenance_mode' => $maintenance_mode,
        'max_reservations_per_day' => $max_reservations_per_day,
        'payment_bank_transfer' => $payment_bank_transfer,
        'payment_bank_account' => $payment_bank_account,
        'payment_qris' => $payment_qris,
        'payment_qris_image' => $payment_qris_image,
        'payment_cash' => $payment_cash,
        'payment_confirmation_required' => $payment_confirmation_required
    ];

    $success = true;

    foreach ($settings_to_update as $key => $value) {
        // Check if setting exists
        $check_sql = "SELECT COUNT(*) as count FROM settings WHERE setting_key = :key";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':key', $key);
        $check_stmt->execute();
        $exists = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

        if ($exists) {
            // Update existing setting
            $update_sql = "UPDATE settings SET setting_value = :value WHERE setting_key = :key";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bindParam(':key', $key);
            $update_stmt->bindParam(':value', $value);

            if (!$update_stmt->execute()) {
                $success = false;
            }
        } else {
            // Insert new setting
            $insert_sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)";
            $insert_stmt = $db->prepare($insert_sql);
            $insert_stmt->bindParam(':key', $key);
            $insert_stmt->bindParam(':value', $value);

            if (!$insert_stmt->execute()) {
                $success = false;
            }
        }
    }

    if ($success) {
        setFlashMessage('message', 'Pengaturan berhasil disimpan.', 'alert alert-success');
    } else {
        setFlashMessage('message', 'Terjadi kesalahan saat menyimpan pengaturan.', 'alert alert-danger');
    }

    redirect("settings.php");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Pengaturan - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Pengaturan" name="keywords">
    <meta content="Admin panel untuk mengelola pengaturan di Taman Kopses Ciseeng" name="description">

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

        .form-label {
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .nav-tabs .nav-link {
            color: var(--dark-text);
            border: none;
            border-bottom: 2px solid transparent;
            padding: 10px 15px;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: transparent;
            border-bottom: 2px solid var(--primary-color);
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            border-bottom: 2px solid var(--primary-light);
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
                    <h1 class="mb-4">Pengaturan</h1>
                    <?php displayFlashMessage(); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                        <i class="fas fa-cog me-2"></i>Umum
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                        <i class="fas fa-address-book me-2"></i>Kontak
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="operation-tab" data-bs-toggle="tab" data-bs-target="#operation" type="button" role="tab" aria-controls="operation" aria-selected="false">
                                        <i class="fas fa-clock me-2"></i>Operasional
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="payment-methods-tab" data-bs-toggle="tab" data-bs-target="#payment-methods" type="button" role="tab" aria-controls="payment-methods" aria-selected="false">
                                        <i class="fas fa-credit-card me-2"></i>Pembayaran
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">
                                        <i class="fas fa-server me-2"></i>Sistem
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="tab-content" id="settingsTabsContent">
                                    <!-- General Settings Tab -->
                                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                        <h5 class="mb-4">Pengaturan Umum</h5>
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Nama Situs</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" required>
                                            <div class="form-text">Nama situs yang akan ditampilkan di judul halaman dan header.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">Deskripsi Situs</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($site_description); ?></textarea>
                                            <div class="form-text">Deskripsi singkat tentang situs yang akan ditampilkan di meta description.</div>
                                        </div>
                                    </div>

                                    <!-- Contact Settings Tab -->
                                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                        <h5 class="mb-4">Pengaturan Kontak</h5>
                                        <div class="mb-3">
                                            <label for="contact_whatsapp" class="form-label">Nomor WhatsApp</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+62</span>
                                                <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" value="<?php echo htmlspecialchars($contact_whatsapp); ?>" placeholder="8123456789">
                                            </div>
                                            <div class="form-text">Nomor WhatsApp untuk kontak (tanpa +62 atau 0).</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact_instagram" class="form-label">Username Instagram</label>
                                            <div class="input-group">
                                                <span class="input-group-text">@</span>
                                                <input type="text" class="form-control" id="contact_instagram" name="contact_instagram" value="<?php echo htmlspecialchars($contact_instagram); ?>" placeholder="tamankopsesciseeng">
                                            </div>
                                            <div class="form-text">Username Instagram tanpa tanda @.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact_facebook" class="form-label">URL Facebook</label>
                                            <input type="text" class="form-control" id="contact_facebook" name="contact_facebook" value="<?php echo htmlspecialchars($contact_facebook); ?>" placeholder="https://www.facebook.com/tamankopsesciseeng">
                                            <div class="form-text">URL lengkap halaman Facebook.</div>
                                        </div>
                                    </div>

                                    <!-- Operation Settings Tab -->
                                    <div class="tab-pane fade" id="operation" role="tabpanel" aria-labelledby="operation-tab">
                                        <h5 class="mb-4">Pengaturan Operasional</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Jam Operasional Hari Kerja (Senin-Jumat)</label>
                                                <div class="row g-2">
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Buka</span>
                                                            <input type="time" class="form-control" id="weekday_start_time" name="weekday_start_time" value="<?php echo htmlspecialchars($weekday_start_time); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Tutup</span>
                                                            <input type="time" class="form-control" id="weekday_end_time" name="weekday_end_time" value="<?php echo htmlspecialchars($weekday_end_time); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Jam Operasional Akhir Pekan (Sabtu-Minggu)</label>
                                                <div class="row g-2">
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Buka</span>
                                                            <input type="time" class="form-control" id="weekend_start_time" name="weekend_start_time" value="<?php echo htmlspecialchars($weekend_start_time); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Tutup</span>
                                                            <input type="time" class="form-control" id="weekend_end_time" name="weekend_end_time" value="<?php echo htmlspecialchars($weekend_end_time); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Jam Operasional Hari Libur</label>
                                                <div class="row g-2">
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Buka</span>
                                                            <input type="time" class="form-control" id="holiday_start_time" name="holiday_start_time" value="<?php echo htmlspecialchars($holiday_start_time); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">Tutup</span>
                                                            <input type="time" class="form-control" id="holiday_end_time" name="holiday_end_time" value="<?php echo htmlspecialchars($holiday_end_time); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="max_reservations_per_day" class="form-label">Maksimum Reservasi per Hari</label>
                                                <input type="number" class="form-control" id="max_reservations_per_day" name="max_reservations_per_day" value="<?php echo htmlspecialchars($max_reservations_per_day); ?>" min="0">
                                                <div class="form-text">Jumlah maksimum reservasi yang diizinkan per hari. Atur 0 untuk tidak ada batas.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Methods Tab -->
                                    <div class="tab-pane fade" id="payment-methods" role="tabpanel" aria-labelledby="payment-methods-tab">
                                        <h5 class="mb-4">Pengaturan Metode Pembayaran</h5>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="payment_confirmation_required" name="payment_confirmation_required" <?php echo $payment_confirmation_required ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="payment_confirmation_required">Wajib Konfirmasi Pembayaran oleh Admin</label>
                                            </div>
                                            <div class="form-text">Jika diaktifkan, reservasi akan tetap berstatus 'pending' sampai admin mengkonfirmasi pembayaran.</div>
                                        </div>
                                        
                                        <hr class="my-4">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Kelola Metode Pembayaran</h5>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
                                                <i class="fas fa-plus me-1"></i> Tambah Metode Pembayaran
                                            </button>
                                        </div>
                                        
                                        <?php
                                        // Fetch all payment methods
                                        $sql = "SELECT * FROM payment_methods ORDER BY is_active DESC, name ASC";
                                        $stmt = $db->prepare($sql);
                                        $stmt->execute();
                                        $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if (count($payment_methods) > 0):
                                            ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama</th>
                                                            <th>Tipe</th>
                                                            <th>Informasi</th>
                                                            <th>Status</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($payment_methods as $method): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($method['name']) ?></td>
                                                                <td>
                                                                    <?php
                                                                    $type_labels = [
                                                                        'bank_transfer' => 'Transfer Bank',
                                                                        'ewallet' => 'E-Wallet',
                                                                        'qris' => 'QRIS',
                                                                        'cash' => 'Tunai',
                                                                        'other' => 'Lainnya'
                                                                    ];
                                                                    echo isset($type_labels[$method['type']]) ? $type_labels[$method['type']] : $method['type'];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($method['type'] == 'bank_transfer' || $method['type'] == 'ewallet'): ?>
                                                                            <?= nl2br(htmlspecialchars($method['account_info'])) ?>
                                                                    <?php elseif ($method['type'] == 'qris' && !empty($method['qr_image'])): ?>
                                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#qrisImageModal" data-img="../uploads/payments/qris/<?= $method['qr_image'] ?>">
                                                                                <img src="../uploads/payments/qris/<?= $method['qr_image'] ?>" alt="QRIS" class="img-thumbnail" style="max-height: 50px;">
                                                                            </a>
                                                                    <?php else: ?>
                                                                            -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge <?= $method['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                                                        <?= $method['is_active'] ? 'Aktif' : 'Tidak Aktif' ?>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-primary edit-payment-method" data-bs-toggle="modal" data-bs-target="#editPaymentMethodModal" data-id="<?= $method['id'] ?>">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-danger delete-payment-method" data-id="<?= $method['id'] ?>" data-name="<?= htmlspecialchars($method['name']) ?>">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                 </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <p class="mb-0">Belum ada metode pembayaran yang ditambahkan. Klik tombol "Tambah Metode Pembayaran" untuk menambahkan metode pembayaran baru.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- System Settings Tab -->
                                    <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                                        <h5 class="mb-4">Pengaturan Sistem</h5>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo $maintenance_mode ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="maintenance_mode">Mode Pemeliharaan</label>
                                            <div class="form-text">Jika diaktifkan, situs akan menampilkan pesan pemeliharaan dan pengguna tidak dapat mengakses fitur reservasi.</div>
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i> Perhatian: Mengaktifkan mode pemeliharaan akan membatasi akses pengguna ke situs.
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
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
    <!-- Add Payment Method Modal -->
    <div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addPaymentMethodForm" action="payment-methods.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPaymentMethodModalLabel">Tambah Metode Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Nama Metode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="add_description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_type" class="form-label">Tipe Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="add_type" name="type" required>
                                <option value="" selected disabled>Pilih Tipe Pembayaran</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="qris">QRIS</option>
                                <option value="cash">Tunai</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        
                        <!-- Dynamic fields based on payment type -->
                        <div id="add_bank_transfer_fields" class="payment-type-fields d-none">
                            <div class="mb-3">
                                <label for="add_account_info" class="form-label">Informasi Rekening <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="add_account_info" name="account_info" rows="2" placeholder="Contoh: BCA 1234567890 a.n. Taman Kopses Ciseeng"></textarea>
                            </div>
                        </div>
                        
                        <div id="add_ewallet_fields" class="payment-type-fields d-none">
                            <div class="mb-3">
                                <label for="add_ewallet_account" class="form-label">Nomor E-Wallet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_ewallet_account" name="ewallet_account" placeholder="Contoh: 081234567890">
                            </div>
                        </div>
                        
                        <div id="add_qris_fields" class="payment-type-fields d-none">
                            <div class="mb-3">
                                <label for="add_qr_image" class="form-label">Upload Gambar QRIS <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="add_qr_image" name="qr_image" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB.</div>
                            </div>
                            <div id="add_qr_preview" class="text-center d-none mt-3">
                                <p class="mb-2">Preview:</p>
                                <img id="add_qr_preview_img" src="" alt="QRIS Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" checked>
                            <label class="form-check-label" for="add_is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Payment Method Modal -->
    <div class="modal fade" id="editPaymentMethodModal" tabindex="-1" aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editPaymentMethodForm" action="payment-methods.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPaymentMethodModalLabel">Edit Metode Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Metode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Tipe Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_type" name="type" required>
                                <option value="" disabled>Pilih Tipe Pembayaran</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="qris">QRIS</option>
                                <option value="cash">Tunai</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        
                        <!-- Dynamic fields based on payment type -->
                        <div id="edit_bank_transfer_fields" class="payment-type-fields d-none">
                            <div class="mb-3">
                                <label for="edit_account_info" class="form-label">Informasi Rekening <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_account_info" name="account_info" rows="2" placeholder="Contoh: BCA 1234567890 a.n. Taman Kopses Ciseeng"></textarea>
                            </div>
                        </div>
                        
                        <div id="edit_ewallet_fields" class="payment-type-fields d-none">
                            <div class="mb-3">
                                <label for="edit_ewallet_account" class="form-label">Nomor E-Wallet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_ewallet_account" name="ewallet_account" placeholder="Contoh: 081234567890">
                            </div>
                        </div>
                        
                        <div id="edit_qris_fields" class="payment-type-fields d-none">
                            <div id="edit_current_qr_image" class="mb-3 d-none">
                                <label class="form-label">Gambar QRIS Saat Ini</label>
                                <div class="text-center">
                                    <img id="edit_current_qr_img" src="" alt="Current QRIS" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_qr_image" class="form-label">Upload Gambar QRIS Baru</label>
                                <input type="file" class="form-control" id="edit_qr_image" name="qr_image" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Format yang didukung: JPG, JPEG, PNG. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah gambar.</div>
                            </div>
                            <div id="edit_qr_preview" class="text-center d-none mt-3">
                                <p class="mb-2">Preview Gambar Baru:</p>
                                <img id="edit_qr_preview_img" src="" alt="QRIS Preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QRIS Image Modal -->
    <div class="modal fade" id="qrisImageModal" tabindex="-1" aria-labelledby="qrisImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisImageModalLabel">QRIS Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrisFullImage" src="" alt="QRIS Full Size" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Payment Method Confirmation Modal -->
    <div class="modal fade" id="deletePaymentMethodModal" tabindex="-1" aria-labelledby="deletePaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletePaymentMethodForm" action="payment-methods.php" method="post">
                    <input type="hidden" id="delete_id" name="id">
                    <input type="hidden" name="action" value="delete">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePaymentMethodModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus metode pembayaran <strong id="delete_name"></strong>?</p>
                        <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for Payment Method Management -->
    <script>
    $(document).ready(function() {
        // Handle QRIS image modal
        $('#qrisImageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imgSrc = button.data('img');
            $('#qrisFullImage').attr('src', imgSrc);
        });
        
        // Handle payment type selection in Add Modal
        $('#add_type').change(function() {
            // Hide all payment type fields
            $('.payment-type-fields').addClass('d-none');
            
            // Show relevant fields based on selected type
            var selectedType = $(this).val();
            switch(selectedType) {
                case 'bank_transfer':
                    $('#add_bank_transfer_fields').removeClass('d-none');
                    break;
                case 'ewallet':
                    $('#add_ewallet_fields').removeClass('d-none');
                    break;
                case 'qris':
                    $('#add_qris_fields').removeClass('d-none');
                    break;
            }
        });
        
        // Handle payment type selection in Edit Modal
        $('#edit_type').change(function() {
            // Hide all payment type fields
            $('.payment-type-fields').addClass('d-none');
            
            // Show relevant fields based on selected type
            var selectedType = $(this).val();
            switch(selectedType) {
                case 'bank_transfer':
                    $('#edit_bank_transfer_fields').removeClass('d-none');
                    break;
                case 'ewallet':
                    $('#edit_ewallet_fields').removeClass('d-none');
                    break;
                case 'qris':
                    $('#edit_qris_fields').removeClass('d-none');
                    break;
            }
        });
        
        // Preview QRIS image in Add Modal
        $('#add_qr_image').change(function() {
            previewImage(this, '#add_qr_preview', '#add_qr_preview_img');
        });
        
        // Preview QRIS image in Edit Modal
        $('#edit_qr_image').change(function() {
            previewImage(this, '#edit_qr_preview', '#edit_qr_preview_img');
        });
        
        // Function to preview uploaded image
        function previewImage(input, previewContainer, previewImg) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $(previewImg).attr('src', e.target.result);
                    $(previewContainer).removeClass('d-none');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                $(previewContainer).addClass('d-none');
            }
        }
        
        // Handle Edit Payment Method button click
        $('.edit-payment-method').click(function() {
            var id = $(this).data('id');
            
            // Fetch payment method data via AJAX
            $.ajax({
                url: 'get-payment-method.php',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var method = response.data;
                        
                        // Populate form fields
                        $('#edit_id').val(method.id);
                        $('#edit_name').val(method.name);
                        $('#edit_description').val(method.description);
                        $('#edit_type').val(method.type).trigger('change');
                        
                        // Set account info based on type
                        if (method.type === 'bank_transfer' || method.type === 'ewallet') {
                            $('#edit_account_info').val(method.account_info);
                            $('#edit_ewallet_account').val(method.account_info);
                        }
                        
                        // Handle QRIS image
                        if (method.type === 'qris' && method.qr_image) {
                            $('#edit_current_qr_img').attr('src', '../uploads/payments/qris/' + method.qr_image);
                            $('#edit_current_qr_image').removeClass('d-none');
                        } else {
                            $('#edit_current_qr_image').addClass('d-none');
                        }
                        
                        // Set active status
                        $('#edit_is_active').prop('checked', method.is_active == 1);
                    } else {
                        alert('Gagal mengambil data metode pembayaran.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data metode pembayaran.');
                }
            });
        });
        
        // Handle Delete Payment Method button click
        $('.delete-payment-method').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            
            $('#delete_id').val(id);
            $('#delete_name').text(name);
            
            $('#deletePaymentMethodModal').modal('show');
        });
    });
    </script>
</body>

</html>
