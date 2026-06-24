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

// Check if reservation exists and belongs to the user
$sql = "SELECT * FROM reservations WHERE id = :id AND user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    setFlashMessage('message', 'Reservasi tidak ditemukan.', 'alert alert-danger');
    redirect("my-tickets.php");
}

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if reservation can be cancelled (only pending reservations can be cancelled)
if ($reservation['status'] != 'pending') {
    setFlashMessage('message', 'Hanya reservasi dengan status menunggu pembayaran yang dapat dibatalkan.', 'alert alert-danger');
    redirect("reservation-detail.php?id=" . $reservation_id);
}

// Cancel the reservation
$sql = "UPDATE reservations SET status = 'cancelled', updated_at = NOW() WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $reservation_id);

if ($stmt->execute()) {
    setFlashMessage('message', 'Reservasi berhasil dibatalkan.', 'alert alert-success');
} else {
    setFlashMessage('message', 'Terjadi kesalahan saat membatalkan reservasi.', 'alert alert-danger');
}

redirect("reservation-detail.php?id=" . $reservation_id);
?>
