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
    redirect("my-tickets.php");
}

$reservation_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get reservation details
$sql = "SELECT r.*, p.name as package_name, p.price_weekday, p.price_weekend, p.description as package_description,
        pc.name as category_name, u.name as user_name, u.whatsapp, u.email 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        JOIN package_categories pc ON p.category_id = pc.id 
        JOIN users u ON r.user_id = u.id
        WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'confirmed'";

$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Tiket tidak ditemukan atau belum dikonfirmasi.', 'alert alert-danger');
    redirect("my-tickets.php");
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
            font-size: 12px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
        }
        
        .invoice-container {
            max-width: 700px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            page-break-after: always;
        }
        
        .invoice-header {
            padding: 15px;
            border-bottom: 1px solid #e9e9e9;
        }
        
        .invoice-title {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e9e9e9;
        }
        
        .invoice-title h2 {
            margin-bottom: 3px;
            color: #4dc387;
            font-size: 18px;
        }
        
        .invoice-title p {
            margin-bottom: 0;
            font-size: 12px;
        }
        
        .invoice-company {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .company-info h4 {
            color: #4dc387;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .company-info p {
            margin-bottom: 3px;
            color: #666;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e9e9e9;
        }
        
        .invoice-details-col {
            flex: 1;
        }
        
        .invoice-details-col h5 {
            color: #4dc387;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .detail-item {
            margin-bottom: 4px;
            display: flex;
            font-size: 11px;
        }
        
        .detail-label {
            font-weight: 600;
            width: 100px;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .invoice-items {
            margin-bottom: 15px;
        }
        
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .invoice-items th {
            background-color: #f8f9fa;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e9e9e9;
        }
        
        .invoice-items td {
            padding: 6px 8px;
            border-bottom: 1px solid #e9e9e9;
        }
        
        .invoice-items .text-right {
            text-align: right;
        }
        
        .invoice-total {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        
        .invoice-total-box {
            width: 200px;
            border: 1px solid #e9e9e9;
            border-radius: 5px;
            padding: 8px;
            background-color: #f8f9fa;
            font-size: 11px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        
        .total-row.grand-total {
            font-weight: 700;
            color: #4dc387;
            font-size: 14px;
            border-top: 1px solid #e9e9e9;
            padding-top: 4px;
            margin-top: 4px;
        }
        
        .invoice-footer {
            text-align: center;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9e9e9;
            font-size: 10px;
            color: #666;
        }
        
        .invoice-footer p {
            margin-bottom: 2px;
        }
        
        .invoice-notes {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 10px;
        }
        
        .invoice-notes h5 {
            color: #4dc387;
            margin-bottom: 5px;
            font-size: 12px;
        }
        
        .invoice-notes p {
            margin-bottom: 2px;
            line-height: 1.3;
        }
        
        .logo {
            max-width: 80px;
            height: auto;
        }
        
        .qr-code {
            text-align: center;
        }
        
        .qr-code img {
            max-width: 80px;
            height: auto;
        }
        
        .qr-code p {
            margin-top: 3px;
            font-size: 10px;
            color: #666;
        }
        
        .print-instructions {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                page-break-after: avoid;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="print-instructions no-print">
            <p class="mb-0"><i class="fas fa-info-circle me-2"></i> Silakan cetak kwitansi ini atau simpan sebagai PDF untuk digunakan saat kunjungan.</p>
        </div>
        
        <div class="print-button no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Cetak Kwitansi
            </button>
            <a href="reservation-detail.php?id=<?php echo $reservation_id; ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
        
        <div class="invoice-container">
            <div class="invoice-header">
                <div class="invoice-title">
                    <h2>KWITANSI PEMBAYARAN</h2>
                    <p>No. <?php echo sprintf('TKC-%05d', $reservation['id']); ?></p>
                </div>
                
                <div class="invoice-company">
                    <div class="company-info">
                        <h4>Taman Kopses Ciseeng</h4>
                        <p>Jl. Raya Ciseeng, Ciseeng</p>
                        <p>Bogor, Jawa Barat</p>
                        <p>Telp: +62 812-3456-7890</p>
                    </div>
                    <div>
                        <img src="../img/logo.png" alt="Taman Kopses Ciseeng Logo" class="logo">
                    </div>
                </div>
                
                <div class="invoice-details">
                    <div class="invoice-details-col">
                        <h5>Informasi Pelanggan</h5>
                        <div class="detail-item">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value"><?php echo $reservation['user_name']; ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Kontak</div>
                            <div class="detail-value"><?php echo $reservation['whatsapp']; ?></div>
                        </div>
                    </div>
                    
                    <div class="invoice-details-col">
                        <h5>Informasi Kunjungan</h5>
                        <div class="detail-item">
                            <div class="detail-label">No. Reservasi</div>
                            <div class="detail-value">#<?php echo $reservation['id']; ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tanggal Kunjungan</div>
                            <div class="detail-value"><?php echo date('d F Y', strtotime($reservation['visit_date'])); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Jenis Hari</div>
                            <div class="detail-value"><?php echo $reservation['is_weekday'] ? 'Weekday' : 'Weekend'; ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Tanggal Reservasi</div>
                            <div class="detail-value"><?php echo date('d F Y', strtotime($reservation['created_at'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="invoice-items">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="45%">Deskripsi</th>
                            <th width="15%">Jumlah</th>
                            <th width="15%" class="text-right">Harga Satuan</th>
                            <th width="20%" class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <strong><?php echo $reservation['package_name']; ?></strong><br>
                                <small class="text-muted"><?php echo $reservation['category_name']; ?></small>
                            </td>
                            <td><?php echo $reservation['num_visitors']; ?> orang</td>
                            <td class="text-right">Rp <?php 
                                $price = $reservation['is_weekday'] ? $reservation['price_weekday'] : $reservation['price_weekend'];
                                echo number_format($price, 0, ',', '.');
                            ?></td>
                            <td class="text-right">Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php if (!empty($facilities)): ?>
                            <tr>
                                <td colspan="5">
                                    <strong>Fasilitas yang tersedia:</strong>
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        <?php foreach ($facilities as $facility): ?>
                                            <span class="badge bg-light text-dark">
                                                <i class="<?php echo $facility['icon']; ?> me-1"></i>
                                                <?php echo $facility['name']; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="invoice-total">
                <div class="invoice-total-box">
                    <div class="total-row">
                        <div>Subtotal</div>
                        <div>Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                    </div>
                    <div class="total-row">
                        <div>Diskon</div>
                        <div>Rp 0</div>
                    </div>
                    <div class="total-row grand-total">
                        <div>Total</div>
                        <div>Rp <?php echo number_format($reservation['total_price'], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="invoice-notes">
                <h5>Catatan</h5>
                <p>1. Kwitansi ini merupakan bukti pembayaran yang sah.</p>
                <p>2. Harap tunjukkan kwitansi ini saat kedatangan.</p>
                <p>3. Reservasi hanya berlaku pada tanggal kunjungan yang telah ditentukan.</p>
                <p>4. Jam operasional: 08.00 - 17.00 WIB.</p>
            </div>
            
            <div class="d-flex justify-content-between align-items-center px-4 pb-4">
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TKCT<?php echo $reservation['id']; ?>-<?php echo date('Ymd', strtotime($reservation['visit_date'])); ?>" alt="QR Code">
                    <p>Scan untuk verifikasi</p>
                </div>
                
                <div class="text-center">
                    <div style="border-bottom: 1px solid #ddd; width: 150px; margin-bottom: 10px;"></div>
                    <p>Tanda Tangan</p>
                </div>
            </div>
            
            <div class="invoice-footer">
                <p>Terima kasih telah memilih Taman Kopses Ciseeng!</p>
                <p>Untuk informasi lebih lanjut, hubungi kami di +62 812-3456-7890</p>
                <p>www.tamankopses.com</p>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
