<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in as cashier or admin
if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if package ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID Paket tidak valid.', 'alert alert-danger');
    redirect("cashier-packages.php");
}

$package_id = $_GET['id'];

// Get package details
$sql = "SELECT * FROM packages WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $package_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Paket tidak ditemukan.', 'alert alert-danger');
    redirect("cashier-packages.php");
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

            // Then, add the selected facilities
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
            redirect("cashier-packages.php");
        } else {
            setFlashMessage('message', 'Terjadi kesalahan saat memperbarui paket.', 'alert alert-danger');
        }
    }
}

// Format currency for display
function formatCurrencyValue($value) {
    return number_format($value, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Paket - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Tirta Sanita Outbound, Admin, Edit Paket" name="keywords">
    <meta content="Admin panel untuk mengedit paket di Tirta Sanita Outbound" name="description">

    <!-- Favicon -->
    <link href="../img/logo.png" rel="icon">

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

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(77, 195, 135, 0.25);
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

        .badge-note {
            display: inline-block;
            padding: 4px 8px;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 10px;
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
                <a class="navbar-brand d-none d-lg-block" href="cashier-dashboard.php">
                    <span>Tirta Sanita Outbound</span>
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
                    <a href="cashier-packages.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Paket</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategori Paket</label>
                                        <select class="form-select" name="package_category_id" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($package['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $category['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Paket</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $package['name']; ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="description" rows="3"><?php echo $package['description']; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Harga Hari Kerja</label>
                                        <input type="text" class="form-control" name="weekday_price" value="<?php echo formatCurrencyValue($package['price_weekday']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Harga Akhir Pekan</label>
                                        <input type="text" class="form-control" name="weekend_price" value="<?php echo formatCurrencyValue($package['price_weekend']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Fasilitas</label>
                                    <div class="border p-3 rounded">
                                        <?php if (empty($facilities)): ?>
                                            <p class="text-muted mb-0">Tidak ada fasilitas tersedia</p>
                                        <?php else: ?>
                                            <?php foreach ($facilities as $facility): ?>
                                                <div class="facility-check">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="facilities[]" 
                                                               value="<?php echo $facility['id']; ?>" 
                                                               id="facility_<?php echo $facility['id']; ?>"
                                                               <?php echo in_array($facility['id'], $package_facilities) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="facility_<?php echo $facility['id']; ?>">
                                                            <span class="facility-icon">
                                                                <i class="fas <?php echo $facility['icon']; ?>"></i>
                                                            </span>
                                                            <?php echo $facility['name']; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               id="is_active" <?php echo $package['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Paket Aktif
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                    <a href="cashier-packages.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i> Batal
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
