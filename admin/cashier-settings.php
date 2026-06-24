<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

// Get all settings
$sql = "SELECT * FROM settings";
$stmt = $db->prepare($sql);
$stmt->execute();
$settings_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$settings = [];
foreach ($settings_rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $site_name = $_POST['site_name'] ?? '';
    $site_description = $_POST['site_description'] ?? '';
    $contact_whatsapp = $_POST['contact_whatsapp'] ?? '';
    $contact_instagram = $_POST['contact_instagram'] ?? '';
    $contact_facebook = $_POST['contact_facebook'] ?? '';
    $weekday_start = $_POST['weekday_start_time'] ?? '';
    $weekday_end = $_POST['weekday_end_time'] ?? '';
    $weekend_start = $_POST['weekend_start_time'] ?? '';
    $weekend_end = $_POST['weekend_end_time'] ?? '';
    $holiday_start = $_POST['holiday_start_time'] ?? '';
    $holiday_end = $_POST['holiday_end_time'] ?? '';
    $max_reservations = $_POST['max_reservations_per_day'] ?? '0';
    
    $tab = $_POST['tab'] ?? 'umum';
    
    try {
        if ($tab == 'umum') {
            updateSetting($db, 'site_name', $site_name);
            updateSetting($db, 'site_description', $site_description);
            setFlashMessage('message', 'Pengaturan umum berhasil disimpan.', 'alert alert-success');
        } elseif ($tab == 'kontak') {
            updateSetting($db, 'contact_whatsapp', $contact_whatsapp);
            updateSetting($db, 'contact_instagram', $contact_instagram);
            updateSetting($db, 'contact_facebook', $contact_facebook);
            setFlashMessage('message', 'Pengaturan kontak berhasil disimpan.', 'alert alert-success');
        } elseif ($tab == 'operasional') {
            updateSetting($db, 'weekday_start_time', $weekday_start);
            updateSetting($db, 'weekday_end_time', $weekday_end);
            updateSetting($db, 'weekend_start_time', $weekend_start);
            updateSetting($db, 'weekend_end_time', $weekend_end);
            updateSetting($db, 'holiday_start_time', $holiday_start);
            updateSetting($db, 'holiday_end_time', $holiday_end);
            updateSetting($db, 'max_reservations_per_day', $max_reservations);
            setFlashMessage('message', 'Pengaturan operasional berhasil disimpan.', 'alert alert-success');
        } elseif ($tab == 'sistem') {
            $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
            updateSetting($db, 'maintenance_mode', $maintenance_mode);
            setFlashMessage('message', 'Pengaturan sistem berhasil disimpan.', 'alert alert-success');
        }
        redirect("cashier-settings.php");
    } catch (Exception $e) {
        setFlashMessage('message', 'Terjadi kesalahan: ' . $e->getMessage(), 'alert alert-danger');
    }
}

function updateSetting($db, $key, $value) {
    $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
            ON DUPLICATE KEY UPDATE setting_value = :value";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':key', $key);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
}

// Get payment methods
$sql = "SELECT * FROM payment_methods ORDER BY name";
$stmt = $db->prepare($sql);
$stmt->execute();
$payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pengaturan - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #4dc387; --primary-dark: #3da876; --primary-light: #e8f5f0; }
        body { font-family: 'Open Sans', sans-serif; background-color: #f8f9fa; min-height: 100vh; display: flex; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Quicksand', sans-serif; font-weight: 700; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-dark); }
        .sidebar { width: 250px; position: fixed; top: 0; left: 0; height: 100vh; z-index: 999; background-color: white; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .sidebar .sidebar-header { padding: 20px; background-color: var(--primary-color); color: white; }
        .sidebar .sidebar-menu { padding: 20px 0; }
        .sidebar .sidebar-menu .nav-link { padding: 12px 20px; color: #2c3e50; border-left: 4px solid transparent; }
        .sidebar .sidebar-menu .nav-link:hover, .sidebar .sidebar-menu .nav-link.active { background-color: var(--primary-light); border-left-color: var(--primary-color); color: var(--primary-color); }
        .sidebar .sidebar-menu .nav-link i { margin-right: 10px; width: 20px; }
        .main-content { margin-left: 250px; padding: 20px; flex: 1; }
        .card { border: none; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .card-header { background-color: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 15px 20px; }
        .card-body { padding: 20px; }
        .navbar { background-color: white; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-family: 'Quicksand', sans-serif; font-weight: 700; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(77, 195, 135, 0.25); }
        .nav-tabs { border-bottom: 2px solid #dee2e6; }
        .nav-tabs .nav-link { color: #6c757d; border: none; }
        .nav-tabs .nav-link:hover { border-bottom: 2px solid var(--primary-color); color: var(--primary-color); }
        .nav-tabs .nav-link.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); background-color: transparent; }
        @media (max-width: 991.98px) {
            .sidebar { margin-left: -250px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
            .main-content.active { margin-left: 250px; }
        }
    </style>
</head>
<body>
    <?php require_once 'sidebar-helper.php'; ?>
    <?php echo generateSidebar(basename($_SERVER['PHP_SELF'])); ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <a class="navbar-brand d-none d-lg-block" href="cashier-dashboard.php"><span>Tirta Sanita Outbound</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12"><h1 class="mb-0">Pengaturan</h1></div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="tab-umum" data-bs-toggle="tab" href="#umum" role="tab">Umum</a></li>
                        <li class="nav-item"><a class="nav-link" id="tab-kontak" data-bs-toggle="tab" href="#kontak" role="tab">Kontak</a></li>
                        <li class="nav-item"><a class="nav-link" id="tab-operasional" data-bs-toggle="tab" href="#operasional" role="tab">Operasional</a></li>
                        <li class="nav-item"><a class="nav-link" id="tab-pembayaran" data-bs-toggle="tab" href="#pembayaran" role="tab">Pembayaran</a></li>
                        <li class="nav-item"><a class="nav-link" id="tab-sistem" data-bs-toggle="tab" href="#sistem" role="tab">Sistem</a></li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Umum -->
                        <div id="umum" class="tab-pane fade show active" role="tabpanel">
                            <form method="post">
                                <input type="hidden" name="tab" value="umum">
                                <div class="mb-3">
                                    <label class="form-label">Nama Situs</label>
                                    <input type="text" class="form-control" name="site_name" value="<?php echo $settings['site_name'] ?? ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Situs</label>
                                    <textarea class="form-control" name="site_description" rows="3"><?php echo $settings['site_description'] ?? ''; ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button>
                            </form>
                        </div>

                        <!-- Tab Kontak -->
                        <div id="kontak" class="tab-pane fade" role="tabpanel">
                            <form method="post">
                                <input type="hidden" name="tab" value="kontak">
                                <div class="mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" name="contact_whatsapp" value="<?php echo $settings['contact_whatsapp'] ?? ''; ?>" placeholder="+62...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Instagram</label>
                                    <input type="text" class="form-control" name="contact_instagram" value="<?php echo $settings['contact_instagram'] ?? ''; ?>" placeholder="@username">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Facebook</label>
                                    <input type="text" class="form-control" name="contact_facebook" value="<?php echo $settings['contact_facebook'] ?? ''; ?>" placeholder="https://facebook.com/...">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button>
                            </form>
                        </div>

                        <!-- Tab Operasional -->
                        <div id="operasional" class="tab-pane fade" role="tabpanel">
                            <form method="post">
                                <input type="hidden" name="tab" value="operasional">
                                <h6 class="mb-3">Jam Kerja (Senin-Jumat)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" class="form-control" name="weekday_start_time" value="<?php echo $settings['weekday_start_time'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" class="form-control" name="weekday_end_time" value="<?php echo $settings['weekday_end_time'] ?? ''; ?>">
                                    </div>
                                </div>

                                <h6 class="mb-3">Jam Kerja (Sabtu-Minggu)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" class="form-control" name="weekend_start_time" value="<?php echo $settings['weekend_start_time'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" class="form-control" name="weekend_end_time" value="<?php echo $settings['weekend_end_time'] ?? ''; ?>">
                                    </div>
                                </div>

                                <h6 class="mb-3">Jam Kerja (Hari Libur)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Buka</label>
                                        <input type="time" class="form-control" name="holiday_start_time" value="<?php echo $settings['holiday_start_time'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jam Tutup</label>
                                        <input type="time" class="form-control" name="holiday_end_time" value="<?php echo $settings['holiday_end_time'] ?? ''; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Maksimum Reservasi per Hari</label>
                                    <input type="number" class="form-control" name="max_reservations_per_day" value="<?php echo $settings['max_reservations_per_day'] ?? '0'; ?>" min="0">
                                </div>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button>
                            </form>
                        </div>

                        <!-- Tab Pembayaran -->
                        <div id="pembayaran" class="tab-pane fade" role="tabpanel">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Kasir dapat melihat metode pembayaran tetapi tidak dapat menambah/menghapus.
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Metode</th>
                                            <th>Deskripsi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payment_methods as $method): ?>
                                            <tr>
                                                <td><?php echo $method['name']; ?></td>
                                                <td><?php echo $method['description']; ?></td>
                                                <td><?php echo $method['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Sistem -->
                        <div id="sistem" class="tab-pane fade" role="tabpanel">
                            <form method="post">
                                <input type="hidden" name="tab" value="sistem">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" 
                                               <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Mode Pemeliharaan
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Aktifkan untuk menutup situs sementara</small>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
                $('.main-content').toggleClass('active');
            });
        });
    </script>
</body>
</html>
