<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('message', 'Anda tidak memiliki akses ke halaman ini.', 'alert alert-danger');
    redirect("index.php");
}

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('message', 'ID reservasi tidak valid.', 'alert alert-danger');
    redirect("reservations.php");
}

$reservation_id = $_GET['id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get reservation details
$sql = "SELECT r.*, p.name as package_name, pc.name as category_name, u.name as user_name, u.whatsapp, u.email 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id AND r.status = 'confirmed'";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan atau belum dikonfirmasi.', 'alert alert-danger');
    redirect("reservations.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Get package facilities
$sql = "SELECT f.name, f.icon 
        FROM package_facilities pf 
        JOIN facilities f ON pf.facility_id = f.id 
        WHERE pf.package_id = :package_id";

$stmt = $db->prepare($sql);
$stmt->bindParam(':package_id', $reservation['package_id']);
$stmt->execute();
$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// No need to log ticket printing
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>E-Ticket #<?php echo $reservation['id']; ?> - Taman Kopses Ciseeng</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="Taman Kopses Ciseeng, E-Ticket, Print" name="keywords" />
    <meta content="E-Ticket untuk kunjungan ke Taman Kopses Ciseeng" name="description" />

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }
        
        .ticket-container {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .ticket-header {
            background-color: #4dc387;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .ticket-header h2 {
            margin-bottom: 0;
            font-size: 28px;
        }
        
        .ticket-body {
            padding: 30px;
        }
        
        .ticket-info {
            margin-bottom: 30px;
        }
        
        .ticket-info-item {
            margin-bottom: 15px;
            display: flex;
            border-bottom: 1px dashed #e9e9e9;
            padding-bottom: 10px;
        }
        
        .ticket-info-label {
            font-weight: 600;
            width: 40%;
            color: #555;
        }
        
        .ticket-info-value {
            width: 60%;
        }
        
        .ticket-qr {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .ticket-qr img {
            max-width: 150px;
            height: auto;
        }
        
        .ticket-footer {
            background-color: #f8f9fa;
            padding: 15px 30px;
            text-align: center;
            border-top: 1px solid #e9e9e9;
            font-size: 14px;
            color: #666;
        }
        
        .ticket-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .ticket-logo img {
            max-width: 120px;
            height: auto;
        }
        
        .facilities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .facility-item {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .ticket-id {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .print-instructions {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 30px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: #fff;
            }
            
            .ticket-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            
            .ticket-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .ticket-footer {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="print-instructions no-print">
            <p class="mb-0"><i class="fas fa-info-circle me-2"></i> Silakan cetak halaman ini atau simpan sebagai PDF untuk digunakan saat kunjungan.</p>
        </div>
        
        <div class="print-button no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Cetak Tiket
            </button>
            <a href="reservation-detail.php?id=<?php echo $reservation_id; ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
        
        <div class="ticket-container">
            <div class="ticket-header">
                <div class="ticket-id">TIKET #<?php echo $reservation['id']; ?></div>
                <h2>E-TICKET</h2>
                <p class="mb-0">Taman Kopses Ciseeng</p>
            </div>
            
            <div class="ticket-body">
                <div class="ticket-logo">
                    <img src="../img/logo.png" alt="Taman Kopses Ciseeng Logo">
                </div>
                
                <div class="ticket-info">
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Nama Pengunjung</div>
                        <div class="ticket-info-value"><?php echo $reservation['user_name']; ?></div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Kontak</div>
                        <div class="ticket-info-value"><?php echo $reservation['whatsapp']; ?></div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Kategori Paket</div>
                        <div class="ticket-info-value"><?php echo $reservation['category_name']; ?></div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Nama Paket</div>
                        <div class="ticket-info-value"><?php echo $reservation['package_name']; ?></div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Tanggal Kunjungan</div>
                        <div class="ticket-info-value"><?php echo date('d F Y', strtotime($reservation['visit_date'])); ?></div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Jumlah Pengunjung</div>
                        <div class="ticket-info-value"><?php echo $reservation['num_visitors']; ?> orang</div>
                    </div>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Total Harga</div>
                        <div class="ticket-info-value">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                    </div>
                    
                    <?php if (!empty($facilities)): ?>
                    <div class="ticket-info-item">
                        <div class="ticket-info-label">Fasilitas</div>
                        <div class="ticket-info-value">
                            <div class="facilities-list">
                                <?php foreach ($facilities as $facility): ?>
                                <div class="facility-item">
                                    <i class="<?php echo $facility['icon']; ?>"></i>
                                    <?php echo $facility['name']; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="ticket-qr">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TKCT<?php echo $reservation['id']; ?>-<?php echo date('Ymd', strtotime($reservation['visit_date'])); ?>" alt="QR Code">
                    <p class="mt-2 mb-0">Scan QR code ini saat kedatangan</p>
                </div>
            </div>
            
            <div class="ticket-footer">
                <p class="mb-1">Tiket ini hanya berlaku untuk tanggal kunjungan yang telah ditentukan.</p>
                <p class="mb-1">Alamat: Jl. Raya Ciseeng, Ciseeng, Bogor, Jawa Barat</p>
                <p class="mb-0">Jam Operasional: 08.00 - 17.00 WIB</p>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
