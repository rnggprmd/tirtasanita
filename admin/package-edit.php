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
$sql = "SELECT * FROM packages WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $package_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Paket tidak ditemukan.', 'alert alert-danger');
    redirect("packages.php");
}

$package = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all package categories
$sql = "SELECT * FROM package_categories ORDER BY name ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all facilities
$sql = "SELECT * FROM facilities ORDER BY name ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get facilities for this package
$sql = "SELECT facility_id FROM package_facilities WHERE package_id = :package_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $package_id);
$stmt->execute();
$package_facilities = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category_id = $_POST['package_category_id'];
    $price_weekday = str_replace('.', '', $_POST['weekday_price']);
    $price_weekend = str_replace('.', '', $_POST['weekend_price']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate input
    if (empty($name)) {
        setFlashMessage('message', 'Nama paket tidak boleh kosong.', 'alert alert-danger');
    } elseif (empty($category_id)) {
        setFlashMessage('message', 'Kategori paket harus dipilih.', 'alert alert-danger');
    } elseif (!is_numeric($price_weekday) || $price_weekday < 0) {
        setFlashMessage('message', 'Harga hari kerja harus berupa angka positif.', 'alert alert-danger');
    } elseif (!is_numeric($price_weekend) || $price_weekend < 0) {
        setFlashMessage('message', 'Harga akhir pekan harus berupa angka positif.', 'alert alert-danger');
    } else {
        // Update package
        $sql = "UPDATE packages SET 
                name = :name, 
                description = :description, 
                category_id = :category_id, 
                price_weekday = :price_weekday, 
                price_weekend = :price_weekend, 
                is_active = :is_active 
                WHERE id = :id";
                
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $package_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':price_weekday', $price_weekday);
        $stmt->bindParam(':price_weekend', $price_weekend);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            // Update package facilities
            // First, delete all existing facilities for this package
            $delete_sql = "DELETE FROM package_facilities WHERE package_id = :package_id";
            $delete_stmt = $db->prepare($delete_sql);
            $delete_stmt->bindParam(':package_id', $package_id);
            $delete_stmt->execute();
            
            // Then, add selected facilities
            if (isset($_POST['facilities']) && is_array($_POST['facilities'])) {
                $insert_sql = "INSERT INTO package_facilities (package_id, facility_id) VALUES (:package_id, :facility_id)";
                $insert_stmt = $db->prepare($insert_sql);
                
                foreach ($_POST['facilities'] as $facility_id) {
                    $insert_stmt->bindParam(':package_id', $package_id);
                    $insert_stmt->bindParam(':facility_id', $facility_id);
                    $insert_stmt->execute();
                }
            }
            
            setFlashMessage('message', 'Paket berhasil diperbarui.', 'alert alert-success');
            redirect("packages.php");
        } else {
            setFlashMessage('message', 'Terjadi kesalahan saat memperbarui paket.', 'alert alert-danger');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Paket - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Edit Paket" name="keywords">
    <meta content="Admin panel untuk mengedit paket di Taman Kopses Ciseeng" name="description">

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

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .facility-check {
            margin-bottom: 10px;
        }

        .facility-check .form-check-label {
            display: flex;
            align-items: center;
        }

        .facility-check .facility-icon {
            margin-right: 10px;
            width: 24px;
            text-align: center;
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
                    <h1 class="mb-0">Edit Paket</h1>
                    <a href="packages.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-12">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $package_id; ?>" method="post">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Paket</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Paket</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($package['name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="package_category_id" class="form-label">Kategori Paket</label>
                                        <select class="form-select" id="package_category_id" name="package_category_id" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo (isset($package['category_id']) && $package['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $category['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($package['description']); ?></textarea>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="weekday_price" class="form-label">Harga Hari Kerja (Rp)</label>
                                        <input type="text" class="form-control" id="weekday_price" name="weekday_price" value="<?php echo isset($package['price_weekday']) ? number_format($package['price_weekday'], 0, ',', '.') : '0'; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="weekend_price" class="form-label">Harga Akhir Pekan (Rp)</label>
                                        <input type="text" class="form-control" id="weekend_price" name="weekend_price" value="<?php echo isset($package['price_weekend']) ? number_format($package['price_weekend'], 0, ',', '.') : '0'; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo (isset($package['is_active']) && $package['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">Paket Aktif</label>
                                    <div class="form-text">Jika tidak diaktifkan, paket tidak akan ditampilkan di halaman pricelist.</div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Fasilitas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($facilities as $facility): ?>
                                        <div class="col-md-4">
                                            <div class="form-check facility-check">
                                                <input class="form-check-input" type="checkbox" id="facility_<?php echo $facility['id']; ?>" name="facilities[]" value="<?php echo $facility['id']; ?>" <?php echo in_array($facility['id'], $package_facilities) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="facility_<?php echo $facility['id']; ?>">
                                                    <span class="facility-icon"><i class="<?php echo $facility['icon']; ?>"></i></span>
                                                    <?php echo $facility['name']; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="packages.php" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
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
            
            // Format currency input
            $('#weekday_price, #weekend_price').on('input', function() {
                var value = $(this).val().replace(/\D/g, '');
                $(this).val(formatNumber(value));
            });
            
            function formatNumber(num) {
                return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            }
        });
    </script>
</body>

</html>
