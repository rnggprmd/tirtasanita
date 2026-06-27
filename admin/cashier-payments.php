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

// Handle payment process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'process_payment') {
        $reservation_id = $_POST['reservation_id'];
        $payment_method_id = $_POST['payment_method_id'];
        $amount = $_POST['amount'];
        
        try {
            // Check if payment already exists
            $check_sql = "SELECT id FROM payments WHERE reservation_id = :reservation_id";
            $check_stmt = $db->prepare($check_sql);
            $check_stmt->bindParam(':reservation_id', $reservation_id);
            $check_stmt->execute();
            
            $order_id = 'OFFLINE-' . $reservation_id . '-' . time();
            
            if ($check_stmt->rowCount() > 0) {
                // Update existing payment
                $update_sql = "UPDATE payments 
                              SET payment_method_id = :method_id, 
                                  amount = :amount, 
                                  transaction_id = :transaction_id, 
                                  status = 'completed',
                                  updated_at = NOW()
                              WHERE reservation_id = :reservation_id";
                $update_stmt = $db->prepare($update_sql);
                $update_stmt->bindParam(':method_id', $payment_method_id);
                $update_stmt->bindParam(':amount', $amount);
                $update_stmt->bindParam(':transaction_id', $order_id);
                $update_stmt->bindParam(':reservation_id', $reservation_id);
                $update_stmt->execute();
            } else {
                // Insert new payment
                $insert_sql = "INSERT INTO payments (reservation_id, payment_method_id, amount, transaction_id, status)
                              VALUES (:reservation_id, :method_id, :amount, :transaction_id, 'completed')";
                $insert_stmt = $db->prepare($insert_sql);
                $insert_stmt->bindParam(':reservation_id', $reservation_id);
                $insert_stmt->bindParam(':method_id', $payment_method_id);
                $insert_stmt->bindParam(':amount', $amount);
                $insert_stmt->bindParam(':transaction_id', $order_id);
                $insert_stmt->execute();
            }
            
            // Update reservation status
            $res_sql = "UPDATE reservations SET status = 'confirmed' WHERE id = :id";
            $res_stmt = $db->prepare($res_sql);
            $res_stmt->bindParam(':id', $reservation_id);
            $res_stmt->execute();
            
            setFlashMessage('message', 'Pembayaran berhasil diproses!', 'alert alert-success');
        } catch (Exception $e) {
            setFlashMessage('message', 'Error: ' . $e->getMessage(), 'alert alert-danger');
        }
    }
}

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get pending reservations (tanpa pembayaran)
$query = "SELECT r.*, p.name as package_name, u.name as user_name, u.whatsapp,
          (SELECT COUNT(*) FROM payments WHERE reservation_id = r.id AND status = 'completed') as has_payment
          FROM reservations r
          JOIN packages p ON r.package_id = p.id
          JOIN users u ON r.user_id = u.id
          WHERE r.status = 'pending' OR r.status = 'confirmed'";

if ($filter === 'pending') {
    $query .= " AND (SELECT COUNT(*) FROM payments WHERE reservation_id = r.id AND status = 'completed') = 0";
}

if (!empty($search)) {
    $query .= " AND (u.name LIKE :search OR u.whatsapp LIKE :search)";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $db->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%");
}
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment methods
$pm_sql = "SELECT * FROM payment_methods WHERE id != 7 ORDER BY id";
$pm_stmt = $db->prepare($pm_sql);
$pm_stmt->execute();
$payment_methods = $pm_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Terima Pembayaran - Kasir</title>
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
    echo generateSidebar('cashier-payments.php');
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
                <div class="col-12">
                    <h1>Terima Pembayaran</h1>
                </div>
            </div>

            <?php displayFlashMessage(); ?>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Cari Reservasi untuk Pembayaran</h5>
                                <small class="text-muted">Untuk reservasi online atau walk-in</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="row g-3">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="search" value="<?php echo $search; ?>" placeholder="Nama atau WhatsApp pelanggan">
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="fas fa-search me-1"></i> Cari
                                        </button>
                                        <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-sync me-1"></i> Reset
                                        </a>
                                    </div>
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
                            <h5 class="mb-0">Daftar Reservasi Menunggu Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($reservations)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p>Tidak ada reservasi yang perlu diproses pembayaran</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Pengguna</th>
                                                <th>Paket</th>
                                                <th>Tanggal</th>
                                                <th>Orang</th>
                                                <th>Total</th>
                                                <th>Status Pembayaran</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations as $res): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $res['id']; ?></strong></td>
                                                    <td>
                                                        <?php echo $res['user_name']; ?><br>
                                                        <small class="text-muted"><?php echo $res['whatsapp']; ?></small>
                                                    </td>
                                                    <td><?php echo $res['package_name']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($res['visit_date'])); ?></td>
                                                    <td class="text-center"><?php echo $res['num_visitors']; ?> orang</td>
                                                    <td><strong><?php echo formatCurrency($res['total_price']); ?></strong></td>
                                                    <td>
                                                        <?php if ($res['has_payment']): ?>
                                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Terbayar</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning"><i class="fas fa-exclamation-circle me-1"></i>Belum Bayar</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!$res['has_payment']): ?>
                                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal" 
                                                                    onclick="setPaymentData(<?php echo $res['id']; ?>, '<?php echo $res['user_name']; ?>', <?php echo $res['total_price']; ?>)">
                                                                <i class="fas fa-money-bill-wave me-1"></i> Terima Bayar
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Sudah bayar</span>
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
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Proses Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Nama Pelanggan</strong></label>
                            <div class="p-3 bg-light rounded" id="user_name_display" style="min-height: 40px; line-height: 24px; border-left: 4px solid var(--primary-color);"></div>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label"><strong>Metode Pembayaran</strong></label>
                            <select class="form-select" id="payment_method" name="payment_method_id" required>
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <?php foreach ($payment_methods as $pm): ?>
                                    <option value="<?php echo $pm['id']; ?>"><?php echo $pm['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Jumlah Pembayaran</strong></label>
                            <div class="p-3 bg-light rounded" id="amount_display_text" style="min-height: 40px; line-height: 24px; font-weight: 600; font-size: 1.1em; color: var(--primary-color); border-left: 4px solid var(--primary-color);"></div>
                        </div>
                        <input type="hidden" name="reservation_id" id="reservation_id">
                        <input type="hidden" name="amount" id="amount_input">
                        <input type="hidden" name="action" value="process_payment">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setPaymentData(id, name, amount) {
            document.getElementById('reservation_id').value = id;
            document.getElementById('user_name_display').textContent = name;
            document.getElementById('amount_input').value = amount;
            document.getElementById('amount_display_text').textContent = 'Rp ' + parseFloat(amount).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        $('#sidebar-toggle').click(function() {
            $('.sidebar').toggleClass('active');
            $('.main-content').toggleClass('active');
        });
    </script>
</body>

</html>
