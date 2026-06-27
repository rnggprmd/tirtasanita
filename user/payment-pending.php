<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect("login.php");
}

$order_id = $_GET['order_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Pembayaran Pending - Tirta Sanita Outbound</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="../img/logo.png" rel="icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Quicksand:wght@600;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
    <style>
        :root {
            --warning-color: #ffc107;
            --warning-dark: #e0a800;
        }

        .pending-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            padding: 20px;
        }

        .pending-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 50px;
            text-align: center;
            max-width: 500px;
        }

        .pending-icon {
            width: 80px;
            height: 80px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        .pending-icon i {
            color: white;
            font-size: 40px;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .pending-card h2 {
            color: #ffc107;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .pending-card p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .info-box {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4dc387;
            color: white;
        }

        .btn-primary:hover {
            background: #3da876;
            color: white;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="pending-container">
        <div class="pending-card">
            <div class="pending-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>

            <h2>Pembayaran Pending</h2>
            <p>Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi dari sistem kami.</p>

            <div class="info-box">
                <p><strong>📌 Perhatian:</strong> Jangan tutup halaman ini sampai proses pembayaran selesai. Status pembayaran akan diperbarui secara otomatis.</p>
            </div>

            <p style="color: #999; font-size: 14px;">No. Pesanan: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>

            <div class="btn-group">
                <a href="dashboard.php" class="btn btn-primary">Lihat Dashboard</a>
                <a href="../index.php" class="btn btn-secondary">Kembali ke Home</a>
            </div>
        </div>
    </div>
</body>

</html>
