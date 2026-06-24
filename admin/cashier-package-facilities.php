<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID Paket tidak valid.', 'alert alert-danger');
    redirect("cashier-packages.php");
}

$package_id = $_GET['id'];

// Get package
$sql = "SELECT * FROM packages WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $package_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Paket tidak ditemukan.', 'alert alert-danger');
    redirect("cashier-packages.php");
}

$package = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all facilities
$sql = "SELECT * FROM facilities ORDER BY name ASC";
$stmt = $db->prepare($sql);
$stmt->execute();
$all_facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get facilities for this package
$sql = "SELECT facility_id FROM package_facilities WHERE package_id = :package_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $package_id);
$stmt->execute();
$package_facilities = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete all existing facilities for this package
    $delete_sql = "DELETE FROM package_facilities WHERE package_id = :package_id";
    $delete_stmt = $db->prepare($delete_sql);
    $delete_stmt->bindParam(':package_id', $package_id);
    $delete_stmt->execute();

    // Add selected facilities
    if (isset($_POST['facilities']) && is_array($_POST['facilities'])) {
        $insert_sql = "INSERT INTO package_facilities (package_id, facility_id) VALUES (:package_id, :facility_id)";
        $insert_stmt = $db->prepare($insert_sql);
        
        foreach ($_POST['facilities'] as $facility_id) {
            $insert_stmt->bindParam(':package_id', $package_id);
            $insert_stmt->bindParam(':facility_id', $facility_id);
            $insert_stmt->execute();
        }
    }
    
    setFlashMessage('message', 'Fasilitas paket berhasil diperbarui.', 'alert alert-success');
    redirect("cashier-packages.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Fasilitas Paket - Tirta Sanita Outbound</title>
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
        .form-check { margin-bottom: 15px; padding: 12px; background-color: #f8f9fa; border-radius: 8px; }
        .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }
        .facility-icon { width: 24px; text-align: center; margin-right: 10px; }
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
                <div class="col-12">
                    <a href="cashier-packages.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Fasilitas Paket: <?php echo $package['name']; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row">
                                    <?php foreach ($all_facilities as $facility): ?>
                                        <div class="col-md-6">
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
                                </div>

                                <div class="mt-4">
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
