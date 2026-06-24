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
$name = $description = $price_weekday = $price_weekend = "";
$category_id = $is_active = "";
$name_err = $category_id_err = $price_weekday_err = $price_weekend_err = "";

// Get package categories
$sql = "SELECT * FROM package_categories ORDER BY name";
$stmt = $db->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Silakan masukkan nama paket.";
    } else {
        $name = sanitize($_POST["name"]);
    }
    
    // Validate category
    if (empty(trim($_POST["category_id"]))) {
        $category_id_err = "Silakan pilih kategori paket.";
    } else {
        $category_id = trim($_POST["category_id"]);
    }
    
    // Validate price weekday
    if (empty(trim($_POST["price_weekday"]))) {
        $price_weekday_err = "Silakan masukkan harga weekday.";
    } elseif (!is_numeric($_POST["price_weekday"]) || $_POST["price_weekday"] < 0) {
        $price_weekday_err = "Harga harus berupa angka positif.";
    } else {
        $price_weekday = trim($_POST["price_weekday"]);
    }
    
    // Validate price weekend
    if (empty(trim($_POST["price_weekend"]))) {
        $price_weekend_err = "Silakan masukkan harga weekend.";
    } elseif (!is_numeric($_POST["price_weekend"]) || $_POST["price_weekend"] < 0) {
        $price_weekend_err = "Harga harus berupa angka positif.";
    } else {
        $price_weekend = trim($_POST["price_weekend"]);
    }
    
    // Get description
    $description = sanitize($_POST["description"]);
    
    // Get is_active
    $is_active = isset($_POST["is_active"]) ? 1 : 0;
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($category_id_err) && empty($price_weekday_err) && empty($price_weekend_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO packages (name, category_id, description, price_weekday, price_weekend, is_active) 
                VALUES (:name, :category_id, :description, :price_weekday, :price_weekend, :is_active)";
        
        if ($stmt = $db->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":category_id", $category_id);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":price_weekday", $price_weekday);
            $stmt->bindParam(":price_weekend", $price_weekend);
            $stmt->bindParam(":is_active", $is_active);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $package_id = $db->lastInsertId();
                setFlashMessage('message', 'Paket berhasil ditambahkan.', 'alert alert-success');
                redirect("package-facilities.php?id=" . $package_id);
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat menambahkan paket.', 'alert alert-danger');
            }
            
            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($db);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Paket - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Paket" name="keywords">
    <meta content="Admin panel untuk mengelola paket di Taman Kopses Ciseeng" name="description">

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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="packages.php">Paket</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Paket</li>
                        </ol>
                    </nav>
                    <h1 class="mb-0">Tambah Paket Baru</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Formulir Tambah Paket</h5>
                        </div>
                        <div class="card-body">
                            <?php displayFlashMessage(); ?>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Kategori Paket <span class="text-danger">*</span></label>
                                        <select class="form-select <?php echo (!empty($category_id_err)) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id" required>
                                            <option value="" selected disabled>Pilih Kategori</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $category['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?php echo $category_id_err; ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo $name; ?>" required>
                                        <div class="invalid-feedback"><?php echo $name_err; ?></div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="price_weekday" class="form-label">Harga Weekday <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control <?php echo (!empty($price_weekday_err)) ? 'is-invalid' : ''; ?>" id="price_weekday" name="price_weekday" value="<?php echo $price_weekday; ?>" min="0" required>
                                            <div class="invalid-feedback"><?php echo $price_weekday_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="price_weekend" class="form-label">Harga Weekend <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control <?php echo (!empty($price_weekend_err)) ? 'is-invalid' : ''; ?>" id="price_weekend" name="price_weekend" value="<?php echo $price_weekend; ?>" min="0" required>
                                            <div class="invalid-feedback"><?php echo $price_weekend_err; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo ($is_active == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Aktif</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="packages.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
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
