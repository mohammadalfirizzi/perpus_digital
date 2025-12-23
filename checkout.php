<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$total_amount = $_POST['total_amount'] ?? 0;
$payment_method = 'transfer'; // bisa disesuaikan

// TESTING
// Simpan order baru
mysqli_query($conn, "
    INSERT INTO orders (user_id, total_amount, status, payment_method)
    VALUES ($user_id, $total_amount, 'paid', '$payment_method')
");

$order_id = mysqli_insert_id($conn);

// Update order_items yang pending
mysqli_query($conn, "
    UPDATE order_items 
    SET order_id = $order_id, status = 'checked_out' 
    WHERE user_id = $user_id AND status = 'pending'
");

// Redirect ke halaman riwayat
header("Location: riwayat.php");
exit;
?>
