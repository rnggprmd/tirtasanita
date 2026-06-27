<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is cashier/admin
if (!isLoggedIn() || (!isCashier() && !isAdmin())) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

$database = new Database();
$db = $database->getConnection();

// Handle update reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_reservation') {
        $reservation_id = $_POST['reservation_id'];
        $status = $_POST['status'];
        $visit_date = $_POST['visit_date'];
        $num_visitors = $_POST['num_visitors'];
        
        try {
            // Get reservation details first
            $get_sql = "SELECT r.*, p.price_weekday, p.price_weekend FROM reservations r 
                       JOIN packages p ON r.package_id = p.id WHERE r.id = :id";
            $get_stmt = $db->prepare($get_sql);
            $get_stmt->bindParam(':id', $reservation_id);
            $get_stmt->execute();
            $reservation = $get_stmt->fetch();
            
            if ($reservation) {
                // Calculate new price
                $day_type = isWeekday($visit_date) ? 'weekday' : 'weekend';
                $price = $day_type === 'weekday' ? $reservation['price_weekday'] : $reservation['price_weekend'];
                $total_price = $price * $num_visitors;
                
                // Update reservation
                $update_sql = "UPDATE reservations 
                              SET status = :status, 
                                  visit_date = :visit_date, 
                                  num_visitors = :num_visitors,
                                  total_price = :total_price,
                                  updated_at = NOW()
                              WHERE id = :id";
                $update_stmt = $db->prepare($update_sql);
                $update_stmt->bindParam(':status', $status);
                $update_stmt->bindParam(':visit_date', $visit_date);
                $update_stmt->bindParam(':num_visitors', $num_visitors);
                $update_stmt->bindParam(':total_price', $total_price);
                $update_stmt->bindParam(':id', $reservation_id);
                
                if ($update_stmt->execute()) {
                    setFlashMessage('message', 'Reservasi berhasil diupdate!', 'alert alert-success');
                } else {
                    setFlashMessage('message', 'Gagal mengupdate reservasi!', 'alert alert-danger');
                }
            }
        } catch (Exception $e) {
            setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }
    } elseif ($action === 'delete_reservation') {
        $reservation_id = $_POST['reservation_id'];
        
        try {
            // Delete related payments first
            $del_pay_sql = "DELETE FROM payments WHERE reservation_id = :id";
            $del_pay_stmt = $db->prepare($del_pay_sql);
            $del_pay_stmt->bindParam(':id', $reservation_id);
            $del_pay_stmt->execute();
            
            // Delete reservation
            $del_sql = "DELETE FROM reservations WHERE id = :id";
            $del_stmt = $db->prepare($del_sql);
            $del_stmt->bindParam(':id', $reservation_id);
            
            if ($del_stmt->execute()) {
                setFlashMessage('message', 'Reservasi berhasil dihapus!', 'alert alert-success');
            } else {
                setFlashMessage('message', 'Gagal menghapus reservasi!', 'alert alert-danger');
            }
        } catch (Exception $e) {
            setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }
    }
}

// Get filter values
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT r.*, p.name as package_name, u.name as user_name, u.whatsapp,
          (SELECT status FROM payments WHERE reservation_id = r.id ORDER BY created_at DESC LIMIT 1) as payment_status
          FROM reservations r
          JOIN packages p ON r.package_id = p.id
          JOIN users u ON r.user_id = u.id
          WHERE 1=1";

$params = [];

if (!empty($status_filter)) {
    $query .= " AND r.status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search)) {
    $query .= " AND (u.name LIKE :search OR u.whatsapp LIKE :search)";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get packages for edit form
$pkg_sql = "SELECT id, name FROM packages ORDER BY name";
$pkg_stmt = $db->prepare($pkg_sql);
$pkg_stmt->execute();
$packages = $pkg_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Reservasi - Kasir</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="../img/logo.png" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin-style.css" rel="stylesheet">
</head>

<body>
    <?php require_once 'sidebar-helper.php'; 
    echo generateSidebar('cashier-reservations.php');
    ?>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top mb-4">
            <div class="container-fluid">
                <button class="btn btn-sm btn-primary me-2" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="cashier-dashboard.php">Kasir</a>
                <div class="ms-auto">
                    <a class="btn btn-sm btn-outline-primary" href="cashier-dashboard.php">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1>Kelola Reservasi</h1>
                    <a href="cashier-add-reservation.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Reservasi Baru
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filter Reservasi</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="row g-3">
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Menunggu Pembayaran</option>
                                        <option value="confirmed" <?php echo ($status_filter == 'confirmed') ? 'selected' : ''; ?>>Terkonfirmasi</option>
                                        <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Selesai</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" class="form-control" id="search" name="search" value="<?php echo $search; ?>" placeholder="Nama atau WhatsApp">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Daftar Reservasi</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($reservations)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p>Tidak ada reservasi</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pengguna</th>
                                                <th>Paket</th>
                                                <th>Tanggal Kunjungan</th>
                                                <th>Jumlah Orang</th>
                                                <th>Total</th>
                                                <th>Status Pembayaran</th>
                                                <th>Status Reservasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations as $res): ?>
                                                <tr>
                                                    <td>#<?php echo $res['id']; ?></td>
                                                    <td>
                                                        <?php echo $res['user_name']; ?><br>
                                                        <small class="text-muted"><?php echo $res['whatsapp']; ?></small>
                                                    </td>
                                                    <td><?php echo $res['package_name']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($res['visit_date'])); ?></td>
                                                    <td><?php echo $res['num_visitors']; ?> orang</td>
                                                    <td><?php echo formatCurrency($res['total_price']); ?></td>
                                                    <td>
                                                        <?php if ($res['payment_status']): ?>
                                                            <span class="badge bg-success">Terbayar</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Belum Bayar</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $status_class = '';
                                                        switch ($res['status']) {
                                                            case 'pending':
                                                                $status_class = 'badge bg-secondary';
                                                                break;
                                                            case 'confirmed':
                                                                $status_class = 'badge bg-success';
                                                                break;
                                                            default:
                                                                $status_class = 'badge bg-info';
                                                        }
                                                        ?>
                                                        <span class="<?php echo $status_class; ?>"><?php echo ucfirst($res['status']); ?></span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" onclick="loadEditModal(<?php echo htmlspecialchars(json_encode($res), ENT_QUOTES, 'UTF-8'); ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="reservation-detail.php?id=<?php echo $res['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
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
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Reservasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_reservation">
                        <input type="hidden" id="edit_reservation_id" name="reservation_id">

                        <div class="mb-3">
                            <label for="edit_visit_date" class="form-label">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="edit_visit_date" name="visit_date" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_num_visitors" class="form-label">Jumlah Pengunjung</label>
                            <input type="number" class="form-control" id="edit_num_visitors" name="num_visitors" value="1" min="1" max="100" required onchange="updateEditPrice()">
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="pending">Menunggu Pembayaran</option>
                                <option value="confirmed">Terkonfirmasi</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <strong>Harga Paket:</strong><br>
                            Harga Per Orang: <span id="edit_price_per_person">Rp 0</span><br>
                            Total Harga Baru: <strong><span id="edit_total_price">Rp 0</span></strong>
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

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store package data globally for price calculation
        let packageData = {};

        // Load data when page loads
        function loadPackageData() {
            <?php 
            $pkg_sql = "SELECT id, price_weekday, price_weekend FROM packages";
            $pkg_stmt = $db->prepare($pkg_sql);
            $pkg_stmt->execute();
            $packages_full = $pkg_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            packageData = <?php echo json_encode($packages_full); ?>;
        }

        // Load edit modal with reservation data
        function loadEditModal(reservation) {
            document.getElementById('edit_reservation_id').value = reservation.id;
            document.getElementById('edit_visit_date').value = reservation.visit_date;
            document.getElementById('edit_num_visitors').value = reservation.num_visitors;
            document.getElementById('edit_status').value = reservation.status;
            
            // Store current package for price calculation
            window.currentPackageId = reservation.package_id;
            updateEditPrice();
        }

        // Update price preview in edit modal
        function updateEditPrice() {
            const numVisitors = parseInt(document.getElementById('edit_num_visitors').value) || 0;
            const visitDate = document.getElementById('edit_visit_date').value;
            
            if (!visitDate || !window.currentPackageId) {
                document.getElementById('edit_price_per_person').textContent = 'Rp 0';
                document.getElementById('edit_total_price').textContent = 'Rp 0';
                return;
            }

            // Find package
            const pkg = packageData.find(p => p.id == window.currentPackageId);
            if (!pkg) return;

            // Check if weekday or weekend
            const date = new Date(visitDate);
            const dayOfWeek = date.getDay();
            const isWeekday = dayOfWeek >= 1 && dayOfWeek <= 5;
            const pricePerPerson = isWeekday ? pkg.price_weekday : pkg.price_weekend;
            const totalPrice = pricePerPerson * numVisitors;

            document.getElementById('edit_price_per_person').textContent = 'Rp ' + pricePerPerson.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            document.getElementById('edit_total_price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        // Initialize
        loadPackageData();

        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });
    </script>
</body>

</html>
