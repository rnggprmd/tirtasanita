<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlashMessage('message', 'Silakan login terlebih dahulu untuk melakukan reservasi.', 'alert alert-danger');
    redirect("login.php");
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Initialize variables
$package_category_id = $package_id = $visit_date = $num_visitors = "";
$package_category_id_err = $package_id_err = $visit_date_err = $num_visitors_err = "";
$edit_mode = false;
$reservation_id = null;
$page_title = "Buat Reservasi Baru";
$submit_button_text = "Buat Reservasi";

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get package categories
$sql = "SELECT * FROM package_categories ORDER BY name";
$stmt = $db->prepare($sql);
$stmt->execute();
$package_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if we're in edit mode
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_mode = true;
    $reservation_id = $_GET['edit'];
    $page_title = "Edit Reservasi";
    $submit_button_text = "Simpan Perubahan";
    
    // Get reservation details
    $sql = "SELECT r.*, p.category_id 
            FROM reservations r 
            JOIN packages p ON r.package_id = p.id 
            WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'pending'";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $reservation_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        setFlashMessage('message', 'Reservasi tidak ditemukan atau tidak dapat diedit.', 'alert alert-danger');
        redirect("my-tickets.php");
    }
    
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Populate form fields with reservation data
    $package_category_id = $reservation['category_id'];
    $package_id = $reservation['package_id'];
    $visit_date = $reservation['visit_date'];
    $num_visitors = $reservation['num_visitors'];
    
    // Get packages for the selected category
    $sql = "SELECT * FROM packages WHERE category_id = :category_id AND is_active = 1 ORDER BY name";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':category_id', $package_category_id);
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get edit mode from form
    $edit_mode = isset($_POST['edit_mode']) && $_POST['edit_mode'] == 'true';
    $reservation_id = $edit_mode ? $_POST['reservation_id'] : null;
    
    // Validate package category
    if (empty(trim($_POST["package_category_id"]))) {
        $package_category_id_err = "Silakan pilih jenis paket.";
    } else {
        $package_category_id = trim($_POST["package_category_id"]);
    }
    
    // Validate package
    if (empty(trim($_POST["package_id"]))) {
        $package_id_err = "Silakan pilih nama paket.";
    } else {
        $package_id = trim($_POST["package_id"]);
    }
    
    // Validate visit date
    if (empty(trim($_POST["visit_date"]))) {
        $visit_date_err = "Silakan pilih tanggal kunjungan.";
    } else {
        $visit_date = trim($_POST["visit_date"]);
        // Check if date is in the past
        if (strtotime($visit_date) < strtotime(date('Y-m-d'))) {
            $visit_date_err = "Tanggal kunjungan tidak boleh di masa lalu.";
        }
    }
    
    // Validate number of visitors
    if (empty(trim($_POST["num_visitors"]))) {
        $num_visitors_err = "Silakan masukkan jumlah pengunjung.";
    } elseif (!is_numeric($_POST["num_visitors"]) || $_POST["num_visitors"] < 1) {
        $num_visitors_err = "Jumlah pengunjung minimal 1 orang.";
    } else {
        $num_visitors = trim($_POST["num_visitors"]);
    }
    
    // Check input errors before creating/updating reservation
    if (empty($package_category_id_err) && empty($package_id_err) && empty($visit_date_err) && empty($num_visitors_err)) {
        
        // Get package details
        $sql = "SELECT * FROM packages WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":id", $package_id);
        $stmt->execute();
        
        if ($package = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Determine if it's weekday or weekend
            $is_weekday = isWeekday($visit_date);
            
            // Calculate total price
            $price = $is_weekday ? $package['price_weekday'] : $package['price_weekend'];
            $total_price = $price * $num_visitors;
            
            if ($edit_mode) {
                // Update existing reservation
                $sql = "UPDATE reservations 
                        SET package_id = :package_id, 
                            visit_date = :visit_date, 
                            num_visitors = :num_visitors, 
                            is_weekday = :is_weekday, 
                            total_price = :total_price, 
                            updated_at = NOW() 
                        WHERE id = :id AND user_id = :user_id AND status = 'pending'";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":id", $reservation_id);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":package_id", $package_id);
                $stmt->bindParam(":visit_date", $visit_date);
                $stmt->bindParam(":num_visitors", $num_visitors);
                $stmt->bindParam(":is_weekday", $is_weekday);
                $stmt->bindParam(":total_price", $total_price);
                
                if ($stmt->execute() && $stmt->rowCount() > 0) {
                    setFlashMessage('message', 'Reservasi berhasil diperbarui!', 'alert alert-success');
                    redirect("reservation-detail.php?id=" . $reservation_id);
                } else {
                    setFlashMessage('message', 'Terjadi kesalahan saat memperbarui reservasi atau tidak ada perubahan yang dilakukan.', 'alert alert-danger');
                }
            } else {
                // Create new reservation
                $sql = "INSERT INTO reservations (user_id, package_id, visit_date, num_visitors, is_weekday, total_price, status) 
                        VALUES (:user_id, :package_id, :visit_date, :num_visitors, :is_weekday, :total_price, 'pending')";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":package_id", $package_id);
                $stmt->bindParam(":visit_date", $visit_date);
                $stmt->bindParam(":num_visitors", $num_visitors);
                $stmt->bindParam(":is_weekday", $is_weekday);
                $stmt->bindParam(":total_price", $total_price);
                
                if ($stmt->execute()) {
                    $reservation_id = $db->lastInsertId();
                    setFlashMessage('message', 'Reservasi berhasil dibuat! Silakan lakukan pembayaran.', 'alert alert-success');
                    redirect("payment.php?id=" . $reservation_id);
                } else {
                    setFlashMessage('message', 'Terjadi kesalahan saat membuat reservasi. Silakan coba lagi.', 'alert alert-danger');
                }
            }
        } else {
            setFlashMessage('message', 'Paket tidak ditemukan.', 'alert alert-danger');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Reservasi - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, Reservasi, Booking" name="keywords" />
    <meta content="Reservasi kunjungan ke Taman Kopses Ciseeng" name="description" />

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
                    <li class="breadcrumb-item text-white active" aria-current="page">Reservasi</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->    

    <!-- Reservation Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="fs-5 fw-bold text-primary">Reservasi Kunjungan</p>
                    <h1 class="display-5 mb-4"><?php echo $page_title; ?></h1>
                    <p class="mb-4">Silakan isi form berikut untuk melakukan reservasi kunjungan ke Taman Kopses Ciseeng. Pilih paket yang sesuai dengan kebutuhan Anda.</p>
                    
                    <div class="bg-primary-light p-4 mb-4 round">
                        <h4 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Penting</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Untuk paket outbound (PAKET ANAK, PAKET DEWASA, dll) minimal pemesanan <strong>35 orang</strong></li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Tiket Regular tidak memiliki batasan minimal pemesanan</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Harga weekend lebih tinggi dibandingkan weekday</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-calendar-check text-white"></i>
                        </div>
                        <div>
                            <h5>Pilih Tanggal Kunjungan</h5>
                            <p class="mb-0">Tentukan tanggal kunjungan Anda</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h5>Pilih Paket</h5>
                            <p class="mb-0">Pilih paket yang sesuai dengan kebutuhan Anda</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center bg-primary rounded-circle" style="width: 50px; height: 50px">
                            <i class="fa fa-money-bill-wave text-white"></i>
                        </div>
                        <div>
                            <h5>Lakukan Pembayaran</h5>
                            <p class="mb-0">Selesaikan pembayaran untuk mengkonfirmasi reservasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-white shadow-sm round p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Formulir Reservasi</h3>
                            <p class="text-muted">Isi data untuk melakukan reservasi kunjungan</p>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        <div id="package-min-warning" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span>Paket ini memerlukan minimal 35 orang</span>
                        </div>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($edit_mode ? '?edit=' . $reservation_id : '')); ?>" method="post" id="reservationForm">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="edit_mode" value="true">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
                            <?php endif; ?>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-tags text-primary"></i></span>
                                    <select class="form-select round-5 border-start-0 ps-0 <?php echo (!empty($package_category_id_err)) ? 'is-invalid' : ''; ?>" id="package_category_id" name="package_category_id" required>
                                        <option value="" selected disabled>Pilih Jenis Paket</option>
                                        <?php foreach ($package_categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" <?php echo ($package_category_id == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo $category['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (!empty($package_category_id_err)): ?><div class="text-danger small mt-1"><?php echo $package_category_id_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-box text-primary"></i></span>
                                    <select class="form-select round-5 border-start-0 ps-0 <?php echo (!empty($package_id_err)) ? 'is-invalid' : ''; ?>" id="package_id" name="package_id" required>
                                        <option value="" selected disabled>Pilih Nama Paket</option>
                                    </select>
                                </div>
                                <?php if (!empty($package_id_err)): ?><div class="text-danger small mt-1"><?php echo $package_id_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-calendar-alt text-primary"></i></span>
                                    <input type="date" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($visit_date_err)) ? 'is-invalid' : ''; ?>" id="visit_date" name="visit_date" value="<?php echo $visit_date; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <?php if (!empty($visit_date_err)): ?><div class="text-danger small mt-1"><?php echo $visit_date_err; ?></div><?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-clock text-primary"></i></span>
                                    <input type="text" class="form-control round-5 border-start-0 ps-0" id="day_type" placeholder="Jenis Hari" readonly>
                                </div>
                                <div class="form-text small">Harga paket berbeda untuk weekday dan weekend</div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white round-5 border-end-0"><i class="fas fa-users text-primary"></i></span>
                                    <input type="number" class="form-control round-5 border-start-0 ps-0 <?php echo (!empty($num_visitors_err)) ? 'is-invalid' : ''; ?>" id="num_visitors" name="num_visitors" value="<?php echo $num_visitors; ?>" min="1" placeholder="Jumlah Pengunjung" required>
                                </div>
                                <?php if (!empty($num_visitors_err)): ?><div class="text-danger small mt-1"><?php echo $num_visitors_err; ?></div><?php endif; ?>
                                <div id="min-visitors-info" class="form-text small d-none">Minimal 35 orang untuk paket ini</div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="bg-primary-light p-3 border rounded round-5">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Harga per orang:</span>
                                        <span id="price_per_person" class="fw-bold">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jumlah pengunjung:</span>
                                        <span id="visitors_count" class="fw-bold">-</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fs-5 fw-bold">Total:</span>
                                        <span id="total_price" class="fs-5 fw-bold text-primary">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-3 rounded-pill" id="submit-btn">
                                    <i class="fas fa-<?php echo $edit_mode ? 'save' : 'check-circle'; ?> me-2"></i> <?php echo $submit_button_text; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                <a href="reservation-detail.php?id=<?php echo $reservation_id; ?>" class="btn btn-outline-secondary rounded-pill">
                                    <i class="fas fa-times-circle me-2"></i> Batal
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reservation End -->

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

    <!-- Custom Javascript -->
    <script>
        $(document).ready(function() {
            // Load packages when category is selected
            $('#package_category_id').change(function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: 'get_packages.php',
                        type: 'POST',
                        data: {category_id: categoryId},
                        dataType: 'json',
                        success: function(data) {
                            $('#package_id').empty();
                            $('#package_id').append('<option value="" selected disabled>Pilih Nama Paket</option>');
                            $.each(data, function(key, value) {
                                $('#package_id').append('<option value="' + value.id + '" data-weekday="' + value.price_weekday + '" data-weekend="' + value.price_weekend + '" data-name="' + value.name + '">' + value.name + '</option>');
                            });
                            updatePricePreview();
                            checkPackageRequirements();
                        }
                    });
                } else {
                    $('#package_id').empty();
                    $('#package_id').append('<option value="" selected disabled>Pilih Nama Paket</option>');
                    updatePricePreview();
                    hideMinVisitorsWarning();
                }
            });

            // Update day type when date is selected
            $('#visit_date').change(function() {
                updateDayType();
                updatePricePreview();
            });

            // Update price preview when package is selected
            $('#package_id').change(function() {
                updatePricePreview();
                checkPackageRequirements();
            });
            
            // Update price preview and validate min visitors when visitors count changes
            $('#num_visitors').change(function() {
                updatePricePreview();
                validateMinVisitors();
            });
            
            // Validate form before submission
            $('#reservationForm').submit(function(e) {
                if (!validateMinVisitors()) {
                    e.preventDefault();
                    $('#package-min-warning').removeClass('d-none');
                    $('html, body').animate({
                        scrollTop: $('#package-min-warning').offset().top - 100
                    }, 300);
                }
            });

            function updateDayType() {
                var date = $('#visit_date').val();
                if (date) {
                    var dayOfWeek = new Date(date).getDay();
                    var isWeekday = (dayOfWeek >= 1 && dayOfWeek <= 5);
                    $('#day_type').val(isWeekday ? 'Weekday' : 'Weekend');
                } else {
                    $('#day_type').val('');
                }
            }

            function updatePricePreview() {
                var packageOption = $('#package_id option:selected');
                var date = $('#visit_date').val();
                var numVisitors = $('#num_visitors').val() || 0;

                if (packageOption.val() && date) {
                    var dayOfWeek = new Date(date).getDay();
                    var isWeekday = (dayOfWeek >= 1 && dayOfWeek <= 5);
                    
                    var pricePerPerson = isWeekday ? 
                        parseFloat(packageOption.data('weekday')) : 
                        parseFloat(packageOption.data('weekend'));
                    
                    var totalPrice = pricePerPerson * numVisitors;
                    
                    $('#price_per_person').text('Rp ' + formatNumber(pricePerPerson));
                    $('#visitors_count').text(numVisitors + ' orang');
                    $('#total_price').text('Rp ' + formatNumber(totalPrice));
                } else {
                    $('#price_per_person').text('-');
                    $('#visitors_count').text('-');
                    $('#total_price').text('-');
                }
            }
            
            function checkPackageRequirements() {
                var packageOption = $('#package_id option:selected');
                if (packageOption.val()) {
                    var packageName = packageOption.data('name');
                    if (packageName && packageName.startsWith('PAKET')) {
                        showMinVisitorsWarning();
                    } else {
                        hideMinVisitorsWarning();
                    }
                } else {
                    hideMinVisitorsWarning();
                }
                validateMinVisitors();
            }
            
            function showMinVisitorsWarning() {
                $('#min-visitors-info').removeClass('d-none');
                $('#num_visitors').attr('min', 35);
                if (!$('#num_visitors').val() || $('#num_visitors').val() < 35) {
                    $('#num_visitors').val(35);
                    updatePricePreview();
                }
            }
            
            function hideMinVisitorsWarning() {
                $('#min-visitors-info').addClass('d-none');
                $('#package-min-warning').addClass('d-none');
                $('#num_visitors').attr('min', 1);
            }
            
            function validateMinVisitors() {
                var packageOption = $('#package_id option:selected');
                if (packageOption.val()) {
                    var packageName = packageOption.data('name');
                    var numVisitors = parseInt($('#num_visitors').val()) || 0;
                    
                    if (packageName && packageName.startsWith('PAKET') && numVisitors < 35) {
                        $('#package-min-warning').removeClass('d-none');
                        return false;
                    } else {
                        $('#package-min-warning').addClass('d-none');
                        return true;
                    }
                }
                return true;
            }

            function formatNumber(num) {
                return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            }

            // Initial update
            updateDayType();
        });
    </script>
</body>

</html>
