<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu.', 'alert alert-danger');
    redirect("login.php");
}

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID reservasi tidak valid.', 'alert alert-danger');
    redirect("dashboard.php");
}

$reservation_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get reservation details
$database = new Database();
$db = $database->getConnection();

$sql = "SELECT r.*, p.name as package_name, p.price_weekday, p.price_weekend, pc.name as category_name 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        WHERE r.id = :id AND r.user_id = :user_id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan.', 'alert alert-danger');
    redirect("dashboard.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Get payment methods
$sql = "SELECT * FROM payment_methods WHERE is_active = 1";
$stmt = $db->prepare($sql);
$stmt->execute();
$payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group payment methods by type
$grouped_methods = [];
foreach ($payment_methods as $method) {
    $type = isset($method['type']) ? $method['type'] : 'other';
    if (!isset($grouped_methods[$type])) {
        $grouped_methods[$type] = [];
    }
    $grouped_methods[$type][] = $method;
}

// Process payment form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method_id = $_POST['payment_method_id'];
    
    // Check if file is uploaded
    if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['proof_of_payment']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $new_filename = 'payment_' . $reservation_id . '_' . time() . '.' . $filetype;
            $upload_path = '../uploads/payments/' . $new_filename;
            
            // Create directory if it doesn't exist
            if (!file_exists('../uploads/payments/')) {
                mkdir('../uploads/payments/', 0777, true);
            }
            
            // Upload file
            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $upload_path)) {
                // Create payment record
                $sql = "INSERT INTO payments (reservation_id, payment_method_id, amount, proof_of_payment, status) 
                        VALUES (:reservation_id, :payment_method_id, :amount, :proof_of_payment, 'pending')";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':reservation_id', $reservation_id);
                $stmt->bindParam(':payment_method_id', $payment_method_id);
                $stmt->bindParam(':amount', $reservation['total_price']);
                $stmt->bindParam(':proof_of_payment', $new_filename);
                
                if ($stmt->execute()) {
                    // Keep reservation status as 'pending' until admin confirms
                    setFlashMessage('message', 'Pembayaran berhasil diupload. Reservasi Anda akan segera dikonfirmasi oleh admin.', 'alert alert-success');
                    redirect("my-tickets.php");
                } else {
                    setFlashMessage('message', 'Terjadi kesalahan saat menyimpan data pembayaran.', 'alert alert-danger');
                }
            } else {
                setFlashMessage('message', 'Terjadi kesalahan saat mengupload bukti pembayaran.', 'alert alert-danger');
            }
        } else {
            setFlashMessage('message', 'Format file tidak didukung. Silakan upload file JPG, JPEG, PNG, atau PDF.', 'alert alert-danger');
        }
    } else {
        setFlashMessage('message', 'Silakan upload bukti pembayaran.', 'alert alert-danger');
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Pembayaran - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Pembayaran, Reservasi" name="keywords" />
    <meta content="Pembayaran reservasi di Taman Kopses Ciseeng" name="description" />

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet" />
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet" />
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet" />
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <?php include_once '../includes/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include_once '../includes/navbar.php'; ?>
    <!-- Navbar End -->

   <!-- Page Header Start -->
   <div class="container-fluid wow bg-primary fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="../index.php">Home</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page">Pembayaran</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Payment Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-4 mb-4">
                        <h3 class="mb-4">Detail Reservasi</h3>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">ID Reservasi</th>
                                    <td width="60%">#<?php echo $reservation['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Paket</th>
                                    <td><?php echo $reservation['category_name']; ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Paket</th>
                                    <td><?php echo $reservation['package_name']; ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kunjungan</th>
                                    <td><?php echo date('d M Y', strtotime($reservation['visit_date'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Hari</th>
                                    <td><?php echo $reservation['is_weekday'] ? 'Weekday' : 'Weekend'; ?></td>
                                </tr>
                                <tr>
                                    <th>Jumlah Pengunjung</th>
                                    <td><?php echo $reservation['num_visitors']; ?> orang</td>
                                </tr>
                                <tr>
                                    <th>Harga per Orang</th>
                                    <td><?php echo formatCurrency($reservation['is_weekday'] ? $reservation['price_weekday'] : $reservation['price_weekend']); ?></td>
                                </tr>
                                <tr>
                                    <th>Total Pembayaran</th>
                                    <td class="fw-bold text-primary"><?php echo formatCurrency($reservation['total_price']); ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php 
                                        $status_class = '';
                                        switch ($reservation['status']) {
                                            case 'pending':
                                                $status_class = 'badge bg-warning';
                                                $status_text = 'Menunggu Pembayaran';
                                                break;
                                            case 'confirmed':
                                                $status_class = 'badge bg-success';
                                                $status_text = 'Terkonfirmasi';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'badge bg-danger';
                                                $status_text = 'Dibatalkan';
                                                break;
                                            case 'completed':
                                                $status_class = 'badge bg-info';
                                                $status_text = 'Selesai';
                                                break;
                                            default:
                                                $status_class = 'badge bg-secondary';
                                                $status_text = $reservation['status'];
                                        }
                                        ?>
                                        <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded p-5">
                        <h3 class="mb-4">Upload Bukti Pembayaran</h3>
                        <?php displayFlashMessage(); ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $reservation_id); ?>" method="post" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="payment_method_id" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                    <option value="" selected disabled>Pilih Metode Pembayaran</option>
                                    <?php foreach ($payment_methods as $method): ?>
                                        <option value="<?php echo $method['id']; ?>" 
                                                data-type="<?php echo isset($method['type']) ? $method['type'] : 'other'; ?>"
                                                data-account="<?php echo isset($method['account_info']) ? htmlspecialchars($method['account_info']) : ''; ?>"
                                                data-qrimage="<?php echo isset($method['qr_image']) ? htmlspecialchars($method['qr_image']) : ''; ?>">
                                            <?php echo $method['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div id="payment-details" class="mb-4 d-none">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Detail Pembayaran</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Bank Transfer Details -->
                                        <div id="bank-details" class="payment-type-details d-none">
                                            <p>Silakan transfer ke rekening berikut:</p>
                                            <div class="bg-light p-3 rounded mb-2">
                                                <p id="bank-account-info" class="mb-0 fw-bold"></p>
                                            </div>
                                        </div>
                                        
                                        <!-- E-Wallet Details -->
                                        <div id="ewallet-details" class="payment-type-details d-none">
                                            <p>Silakan transfer ke nomor e-wallet berikut:</p>
                                            <div class="bg-light p-3 rounded mb-2">
                                                <p id="ewallet-account-info" class="mb-0 fw-bold"></p>
                                            </div>
                                        </div>
                                        
                                        <!-- QRIS Details -->
                                        <div id="qris-details" class="payment-type-details d-none text-center">
                                            <p>Silakan scan kode QRIS berikut:</p>
                                            <div id="qris-image-container" class="mb-2">
                                                <img id="qris-image" src="" alt="QRIS Code" class="img-fluid" style="max-width: 200px;">
                                            </div>
                                            <div id="qris-no-image" class="bg-light p-3 rounded mb-2 d-none">
                                                <p class="mb-0 text-muted">Gambar QRIS tidak tersedia</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Cash Details -->
                                        <div id="cash-details" class="payment-type-details d-none">
                                            <p class="mb-0">Pembayaran tunai dapat dilakukan langsung di lokasi Taman Kopses Ciseeng.</p>
                                        </div>
                                        
                                        <!-- Other Details -->
                                        <div id="other-details" class="payment-type-details d-none">
                                            <p id="other-info" class="mb-0"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="proof_of_payment" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, PDF. Maksimal 2MB.</small>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> yang berlaku.
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3">Upload Bukti Pembayaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment End -->

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Syarat dan Ketentuan Reservasi Taman Kopses Ciseeng</h5>
                    <ol>
                        <li>Pembayaran harus dilakukan dalam waktu 24 jam setelah reservasi dibuat.</li>
                        <li>Pembatalan reservasi yang telah dikonfirmasi akan dikenakan biaya administrasi sebesar 10% dari total pembayaran.</li>
                        <li>Perubahan tanggal kunjungan dapat dilakukan maksimal 3 hari sebelum tanggal kunjungan yang telah dipesan.</li>
                        <li>E-ticket akan dikirimkan setelah pembayaran dikonfirmasi oleh admin.</li>
                        <li>E-ticket harus ditunjukkan saat check-in di lokasi.</li>
                        <li>Harga dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu.</li>
                        <li>Dengan melakukan reservasi, pengunjung dianggap telah membaca dan menyetujui seluruh syarat dan ketentuan yang berlaku.</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include_once '../includes/footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    
    <!-- Payment Method Selection Script -->
    <script>
    $(document).ready(function() {
        // Handle payment method selection
        $('#payment_method_id').change(function() {
            // Hide all payment details first
            $('.payment-type-details').addClass('d-none');
            
            // Get selected option
            var selectedOption = $(this).find('option:selected');
            if (selectedOption.val() === '') {
                $('#payment-details').addClass('d-none');
                return;
            }
            
            // Show payment details container
            $('#payment-details').removeClass('d-none');
            
            // Get payment method data
            var type = selectedOption.data('type');
            var account = selectedOption.data('account');
            var qrImage = selectedOption.data('qrimage');
            
            // Show relevant details based on payment type
            switch(type) {
                case 'bank_transfer':
                    $('#bank-details').removeClass('d-none');
                    $('#bank-account-info').text(account);
                    break;
                    
                case 'ewallet':
                    $('#ewallet-details').removeClass('d-none');
                    $('#ewallet-account-info').text(account);
                    break;
                    
                case 'qris':
                    $('#qris-details').removeClass('d-none');
                    if (qrImage) {
                        $('#qris-image').attr('src', '../uploads/payments/qris/' + qrImage);
                        $('#qris-image-container').removeClass('d-none');
                        $('#qris-no-image').addClass('d-none');
                    } else {
                        $('#qris-image-container').addClass('d-none');
                        $('#qris-no-image').removeClass('d-none');
                    }
                    break;
                    
                case 'cash':
                    $('#cash-details').removeClass('d-none');
                    break;
                    
                default:
                    $('#other-details').removeClass('d-none');
                    $('#other-info').text(selectedOption.text());
                    break;
            }
        });
    });
    </script>
</body>

</html>
