<?php
session_start();
include 'config.php';
// 
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    
    // Ambil detail buku
    $query = "SELECT price FROM books WHERE id = $book_id";
    $result = mysqli_query($conn, $query);
    $book = mysqli_fetch_assoc($result);

    if ($book) {
        $price = $book['price'];

        // Cek apakah item ini sudah ada di order_items pending
        $check = mysqli_query($conn, "SELECT * FROM order_items WHERE user_id = $user_id AND book_id = $book_id AND status = 'pending'");
        if (mysqli_num_rows($check) > 0) {
            // Update quantity
            mysqli_query($conn, "UPDATE order_items SET quantity = quantity + 1 WHERE user_id = $user_id AND book_id = $book_id AND status = 'pending'");
        } else {
            // Insert baru
            mysqli_query($conn, "INSERT INTO order_items (user_id, book_id, quantity, price, status) VALUES ($user_id, $book_id, 1, $price, 'pending')");
        }
    }

    header("Location: index.php#koleksi");
    exit;
}
?>
