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

// Process delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if facility exists
    $sql = "SELECT * FROM facilities WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Check if facility is used in any package
        $check_sql = "SELECT COUNT(*) as count FROM package_facilities WHERE facility_id = :id";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':id', $id);
        $check_stmt->execute();
        $is_used = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($is_used) {
            setFlashMessage('message', 'Fasilitas tidak dapat dihapus karena sedang digunakan dalam paket.', 'alert alert-danger');
        } else {
            // Delete facility
            $delete_sql = "DELETE FROM facilities WHERE id = :id";
            $delete_stmt = $db->prepare($delete_sql);
            $delete_stmt->bindParam(':id', $id);
            
            if ($delete_stmt->execute()) {
                setFlashMessage('message', 'Fasilitas berhasil dihapus.', 'alert alert-success');
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat menghapus fasilitas.', 'alert alert-danger');
            }
        }
    } else {
        setFlashMessage('message', 'Fasilitas tidak ditemukan.', 'alert alert-danger');
    }
    
    redirect("facilities.php");
}

// Process form submission for adding/editing facility
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    
    // Validate input
    if (empty($name)) {
        setFlashMessage('message', 'Nama fasilitas tidak boleh kosong.', 'alert alert-danger');
    } else {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing facility
            $id = $_POST['id'];
            $sql = "UPDATE facilities SET name = :name, description = :description, icon = :icon WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':icon', $icon);
            
            if ($stmt->execute()) {
                setFlashMessage('message', 'Fasilitas berhasil diperbarui.', 'alert alert-success');
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat memperbarui fasilitas.', 'alert alert-danger');
            }
        } else {
            // Add new facility
            $sql = "INSERT INTO facilities (name, description, icon) VALUES (:name, :description, :icon)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':icon', $icon);
            
            if ($stmt->execute()) {
                setFlashMessage('message', 'Fasilitas berhasil ditambahkan.', 'alert alert-success');
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat menambahkan fasilitas.', 'alert alert-danger');
            }
        }
        
        redirect("facilities.php");
    }
}

// Get facility for editing if ID is provided
$edit_id = '';
$edit_name = '';
$edit_description = '';
$edit_icon = '';

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM facilities WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $facility = $stmt->fetch(PDO::FETCH_ASSOC);
        $edit_id = $facility['id'];
        $edit_name = $facility['name'];
        $edit_description = $facility['description'];
        $edit_icon = $facility['icon'];
    }
}

// Handle search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all facilities with usage count and search filter
if (!empty($search)) {
    $sql = "SELECT f.*, (SELECT COUNT(*) FROM package_facilities WHERE facility_id = f.id) as usage_count 
           FROM facilities f 
           WHERE f.name LIKE :search OR f.description LIKE :search 
           ORDER BY f.name ASC";
    $stmt = $db->prepare($sql);
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
} else {
    $sql = "SELECT f.*, (SELECT COUNT(*) FROM package_facilities WHERE facility_id = f.id) as usage_count 
           FROM facilities f 
           ORDER BY f.name ASC";
    $stmt = $db->prepare($sql);
}
$stmt->execute();
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Fasilitas - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Taman Kopses Ciseeng, Admin, Fasilitas" name="keywords">
    <meta content="Admin panel untuk mengelola fasilitas di Taman Kopses Ciseeng" name="description">

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
                <div class="col-12">
                    <h1 class="mb-4">Kelola Fasilitas</h1>
                    <?php displayFlashMessage(); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo !empty($edit_id) ? 'Edit Fasilitas' : 'Tambah Fasilitas'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <?php if (!empty($edit_id)): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Fasilitas</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_name); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($edit_description); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Ikon (Font Awesome)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i id="icon-preview" class="<?php echo !empty($edit_icon) ? $edit_icon : 'fas fa-swimming-pool'; ?>"></i></span>
                                        <input type="text" class="form-control" id="icon" name="icon" value="<?php echo htmlspecialchars($edit_icon); ?>" placeholder="fas fa-swimming-pool">
                                    </div>
                                    <div class="form-text">Masukkan kelas ikon Font Awesome, contoh: fas fa-swimming-pool</div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo !empty($edit_id) ? 'Perbarui Fasilitas' : 'Tambah Fasilitas'; ?>
                                    </button>
                                    <?php if (!empty($edit_id)): ?>
                                        <a href="facilities.php" class="btn btn-outline-secondary">Batal</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Bantuan Ikon</h5>
                        </div>
                        <div class="card-body">
                            <p>Beberapa contoh ikon yang dapat digunakan:</p>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <i class="fas fa-swimming-pool icon-preview"></i> fas fa-swimming-pool
                                </div>
                                <div class="col-6 mb-2">
                                    <i class="fas fa-wifi icon-preview"></i> fas fa-wifi
                                </div>
                                <div class="col-6 mb-2">
                                    <i class="fas fa-parking icon-preview"></i> fas fa-parking
                                </div>
                                <div class="col-6 mb-2">
                                    <i class="fas fa-utensils icon-preview"></i> fas fa-utensils
                                </div>
                                <div class="col-6 mb-2">
                                    <i class="fas fa-coffee icon-preview"></i> fas fa-coffee
                                </div>
                                <div class="col-6 mb-2">
                                    <i class="fas fa-shower icon-preview"></i> fas fa-shower
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="https://fontawesome.com/v5/search" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Semua Ikon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Daftar Fasilitas</h5>
                            <span class="badge bg-primary"><?php echo count($facilities); ?> fasilitas</span>
                        </div>
                        <div class="card-body">
                            <!-- Search Form -->
                            <form action="" method="GET" class="mb-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari fasilitas..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (!empty($search)): ?>
                                        <a href="facilities.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Reset
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                            
                            <?php if (empty($facilities)): ?>
                                <div class="text-center py-3">
                                    <?php if (!empty($search)): ?>
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">Tidak ada fasilitas yang cocok dengan pencarian "<?php echo htmlspecialchars($search); ?>"</p>
                                        <a href="facilities.php" class="btn btn-sm btn-primary mt-3">Tampilkan semua fasilitas</a>
                                    <?php else: ?>
                                        <i class="fas fa-swimming-pool fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">Belum ada fasilitas yang ditambahkan</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php if (!empty($search)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> Menampilkan <?php echo count($facilities); ?> hasil pencarian untuk "<?php echo htmlspecialchars($search); ?>"
                                        <a href="facilities.php" class="float-end">Tampilkan semua</a>
                                    </div>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="10%">Ikon</th>
                                                <th width="25%">Nama</th>
                                                <th width="35%">Deskripsi</th>
                                                <th width="10%">Digunakan</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($facilities as $facility): ?>
                                                <tr>
                                                    <td><?php echo $facility['id']; ?></td>
                                                    <td><i class="<?php echo $facility['icon']; ?> fa-lg"></i></td>
                                                    <td><?php echo $facility['name']; ?></td>
                                                    <td><?php echo !empty($facility['description']) ? $facility['description'] : '<span class="text-muted">Tidak ada deskripsi</span>'; ?></td>
                                                    <td>
                                                        <?php if ($facility['usage_count'] > 0): ?>
                                                            <span class="badge bg-info"><?php echo $facility['usage_count']; ?> paket</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Tidak digunakan</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="facilities.php?action=edit&id=<?php echo $facility['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($facility['usage_count'] == 0): ?>
                                                            <a href="facilities.php?action=delete&id=<?php echo $facility['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-danger" disabled title="Fasilitas sedang digunakan dalam paket">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
            
            // Preview icon
            $('#icon').on('input', function() {
                var iconClass = $(this).val();
                if (iconClass) {
                    $('#icon-preview').attr('class', iconClass);
                } else {
                    $('#icon-preview').attr('class', 'fas fa-swimming-pool');
                }
            });
        });
    </script>
</body>

</html>
