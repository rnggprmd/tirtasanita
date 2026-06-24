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

// Check if package ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID Paket tidak valid.', 'alert alert-danger');
    redirect("packages.php");
}

$package_id = $_GET['id'];

// Get package details
$sql = "SELECT p.*, pc.name as category_name 
        FROM packages p 
        JOIN package_categories pc ON p.category_id = pc.id 
        WHERE p.id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $package_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Paket tidak ditemukan.', 'alert alert-danger');
    redirect("packages.php");
}

$package = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all facilities
$sql = "SELECT * FROM facilities ORDER BY name ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$all_facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get facilities for this package
$sql = "SELECT f.* 
        FROM facilities f 
        JOIN package_facilities pf ON f.id = pf.facility_id 
        WHERE pf.package_id = :package_id 
        ORDER BY f.name ASC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $package_id);
$stmt->execute();
$package_facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get facilities not in this package
$sql = "SELECT f.* 
        FROM facilities f 
        WHERE f.id NOT IN (
            SELECT facility_id FROM package_facilities WHERE package_id = :package_id
        ) 
        ORDER BY f.name ASC";
$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $package_id);
$stmt->execute();
$available_facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process add facility request
if (isset($_POST['action']) && $_POST['action'] == 'add' && isset($_POST['facility_id'])) {
    $facility_id = $_POST['facility_id'];
    
    // Check if facility exists
    $check_sql = "SELECT * FROM facilities WHERE id = :id";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':id', $facility_id);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        // Check if facility is already added to this package
        $check_package_sql = "SELECT * FROM package_facilities WHERE package_id = :package_id AND facility_id = :facility_id";
        $check_package_stmt = $db->prepare($check_package_sql);
        $check_package_stmt->bindParam(':package_id', $package_id);
        $check_package_stmt->bindParam(':facility_id', $facility_id);
        $check_package_stmt->execute();
        
        if ($check_package_stmt->rowCount() == 0) {
            // Add facility to package
            $insert_sql = "INSERT INTO package_facilities (package_id, facility_id) VALUES (:package_id, :facility_id)";
            $insert_stmt = $db->prepare($insert_sql);
            $insert_stmt->bindParam(':package_id', $package_id);
            $insert_stmt->bindParam(':facility_id', $facility_id);
            
            if ($insert_stmt->execute()) {
                setFlashMessage('message', 'Fasilitas berhasil ditambahkan ke paket.', 'alert alert-success');
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat menambahkan fasilitas.', 'alert alert-danger');
            }
        } else {
            setFlashMessage('message', 'Fasilitas sudah ada dalam paket ini.', 'alert alert-warning');
        }
    } else {
        setFlashMessage('message', 'Fasilitas tidak ditemukan.', 'alert alert-danger');
    }
    
    redirect("package-facilities.php?id=" . $package_id);
}

// Process remove facility request
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['facility_id'])) {
    $facility_id = $_GET['facility_id'];
    
    // Remove facility from package
    $delete_sql = "DELETE FROM package_facilities WHERE package_id = :package_id AND facility_id = :facility_id";
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->bindParam(':package_id', $package_id);
    $delete_stmt->bindParam(':facility_id', $facility_id);
    
    if ($delete_stmt->execute()) {
        setFlashMessage('message', 'Fasilitas berhasil dihapus dari paket.', 'alert alert-success');
    } else {
        setFlashMessage('message', 'Terjadi kesalahan saat menghapus fasilitas.', 'alert alert-danger');
    }
    
    redirect("package-facilities.php?id=" . $package_id);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Fasilitas Paket - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Fasilitas Paket" name="keywords">
    <meta content="Admin panel untuk mengelola fasilitas paket di Taman Kopses Ciseeng" name="description">

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

        .facility-card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
        }

        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .facility-card .card-body {
            padding: 15px;
        }

        .facility-card .facility-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .facility-card .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.9);
            color: #dc3545;
            border: none;
            opacity: 0;
            transition: all 0.3s;
        }

        .facility-card:hover .btn-remove {
            opacity: 1;
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
                    <h1 class="mb-0">Kelola Fasilitas Paket</h1>
                    <div>
                        <a href="package-edit.php?id=<?php echo $package_id; ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i> Edit Paket
                        </a>
                        <a href="packages.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Paket</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nama Paket</label>
                                        <p><?php echo isset($package['name']) ? htmlspecialchars($package['name']) : ''; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <p><?php echo isset($package['category_name']) ? htmlspecialchars($package['category_name']) : ''; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p>
                                            <?php if (isset($package['is_active']) && $package['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Harga Hari Kerja</label>
                                        <p><?php echo isset($package['price_weekday']) ? formatCurrency($package['price_weekday']) : formatCurrency(0); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Harga Akhir Pekan</label>
                                        <p><?php echo isset($package['price_weekend']) ? formatCurrency($package['price_weekend']) : formatCurrency(0); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Maksimum Pengunjung</label>
                                        <p><?php echo isset($package['max_visitors']) ? $package['max_visitors'] : '0'; ?> orang</p>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($package['description'])): ?>
                                <div class="mb-0">
                                    <label class="form-label fw-bold">Deskripsi</label>
                                    <p><?php echo isset($package['description']) ? nl2br(htmlspecialchars($package['description'])) : ''; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Fasilitas Paket</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($package_facilities)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-swimming-pool fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">Belum ada fasilitas yang ditambahkan ke paket ini</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($package_facilities as $facility): ?>
                                        <div class="col-md-3 col-sm-6 mb-4">
                                            <div class="card facility-card position-relative">
                                                <div class="card-body text-center">
                                                    <div class="facility-icon">
                                                        <i class="<?php echo $facility['icon']; ?>"></i>
                                                    </div>
                                                    <h5 class="card-title"><?php echo $facility['name']; ?></h5>
                                                    <?php if (!empty($facility['description'])): ?>
                                                        <p class="card-text small text-muted"><?php echo $facility['description']; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="package-facilities.php?id=<?php echo $package_id; ?>&action=remove&facility_id=<?php echo $facility['id']; ?>" class="btn-remove" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini dari paket?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tambah Fasilitas</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($available_facilities)): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p class="mb-0">Semua fasilitas sudah ditambahkan ke paket ini</p>
                                </div>
                            <?php else: ?>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $package_id; ?>" method="post" class="row">
                                    <div class="col-md-8">
                                        <select class="form-select" name="facility_id" required>
                                            <option value="">Pilih Fasilitas</option>
                                            <?php foreach ($available_facilities as $facility): ?>
                                                <option value="<?php echo $facility['id']; ?>">
                                                    <?php echo $facility['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-plus me-2"></i> Tambah Fasilitas
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="mt-4">
                                    <h6 class="mb-3">Fasilitas yang Tersedia:</h6>
                                    <div class="row">
                                        <?php foreach ($available_facilities as $facility): ?>
                                            <div class="col-md-3 col-sm-6 mb-3">
                                                <div class="card facility-card h-100">
                                                    <div class="card-body text-center">
                                                        <div class="facility-icon">
                                                            <i class="<?php echo $facility['icon']; ?>"></i>
                                                        </div>
                                                        <h6 class="card-title"><?php echo $facility['name']; ?></h6>
                                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $package_id; ?>" method="post">
                                                            <input type="hidden" name="action" value="add">
                                                            <input type="hidden" name="facility_id" value="<?php echo $facility['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary mt-2">
                                                                <i class="fas fa-plus me-1"></i> Tambahkan
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
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
