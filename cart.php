<?php
session_start();
include 'config.php';
// 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* --- Hapus item jika dipilih --- */
if (isset($_POST['delete_selected']) && !empty($_POST['delete_items'])) {
    $delete_ids = array_map('intval', $_POST['delete_items']);
    $ids_str = implode(',', $delete_ids);

    $delete_query = "DELETE FROM order_items WHERE user_id = $user_id AND id IN ($ids_str) AND status = 'pending'";
    mysqli_query($conn, $delete_query);

    $_SESSION['success_msg'] = "Item yang dipilih berhasil dihapus dari keranjang.";
    header("Location: cart.php");
    exit;
}

/* --- Ambil item pending --- */
$query = "
SELECT oi.*, b.title, b.cover_image 
FROM order_items oi
JOIN books b ON oi.book_id = b.id
WHERE oi.user_id = $user_id AND oi.status = 'pending'
";
$result = mysqli_query($conn, $query);
$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>🛒 Keranjang Belanja</h2>

    <?php if (!empty($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3">
            <?= htmlspecialchars($_SESSION['success_msg']); unset($_SESSION['success_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <form method="POST" action="cart.php">
            <table class="table table-bordered align-middle mt-3">
                <thead class="table-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Gambar</th>
                        <th>Judul Buku</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($result)): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="delete_items[]" value="<?= $item['id'] ?>">
                            </td>
                            <td width="100">
                                <img src="<?= htmlspecialchars($item['cover_image']) ?>" class="img-fluid rounded">
                            </td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <h4>Total: Rp <?= number_format($total, 0, ',', '.') ?></h4>
                <button type="submit" name="delete_selected" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus yang Dipilih
                </button>
            </div>
        </form>

        <!-- ✅ Form checkout terpisah -->
        <form method="POST" action="checkout.php" class="mt-3">
            <input type="hidden" name="total_amount" value="<?= $total ?>">
            <button type="submit" class="btn btn-success w-100">
                Checkout Sekarang
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning mt-3">Keranjang kosong 😅</div>
    <?php endif; ?>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
document.getElementById('select-all')?.addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('input[name="delete_items[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
</body>
</html>
