<?php
session_start();

// Jika belum login, arahkan ke login.php
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

$is_logged_in = isset($_SESSION['user_name']);
$user_name = $is_logged_in ? $_SESSION['user_name'] : null;
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;


// Ambil Buku
// Ambil semua data buku
include 'config.php'; // koneksi ke database
$query = "SELECT * FROM books ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Gagal mengambil data buku: " . mysqli_error($conn));
}

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query2 = "SELECT SUM(quantity) AS total_items FROM order_items WHERE user_id = $user_id AND status = 'pending'";
    $result2 = mysqli_query($conn, $query2);
    $row = mysqli_fetch_assoc($result2);
    $cart_count = $row['total_items'] ?? 0;
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- AOS Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }


        .navbar-brand {
            font-weight: bold;
        }

        .nav-link {
            font-size: 1.1rem;
        }

        .carousel-item img {
            height: 400px;
            /* Sesuaikan tinggi slider */
            object-fit: cover;
            /* Pastikan gambar terisi penuh */
            filter: brightness(50%);
            /* Mengurangi kecerahan gambar */
        }

        .card-img-top {
            width: 100%;
            /* Agar gambar responsif dalam card */
            height: 400px;
            /* Tinggi tetap 400px */
            object-fit: cover;
            /* Memastikan gambar menyesuaikan ukuran tanpa distorsi */
        }

        .bg-light-gray {
            background-color: #f8f9fa;
            /* Warna abu-abu terang */
            padding: 50px 0;
            /* Padding atas dan bawah */
        }

        .wa-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            transition: background 0.3s ease-in-out;
        }

        .wa-float:hover {
            background-color: #1ebe57;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">📚 Perpustakaan Digital</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#koleksi">Koleksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item position-relative">
    <a class="nav-link text-white" href="cart.php">
        <i class="fas fa-shopping-cart fs-5"></i>
        <?php if ($cart_count >= 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                <?= $cart_count ?>
            </span>
        <?php 
    endif; ?>
    </a>
</li>


                </ul>


                <!-- Form Pencarian -->
                <div class="d-flex ms-3">
                    <input id="searchInput" class="form-control me-2" type="search" placeholder="Cari buku..."
                        aria-label="Search">
                    <button class="btn btn-light" onclick="searchBook()">Cari</button>
                </div>

                <!-- Bagian User/Login -->
                <?php if ($is_logged_in): ?>
                    <ul class="navbar-nav ms-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($user_name) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <h6 class="dropdown-header">👤 <?= htmlspecialchars($user_role) ?></h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-3">
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-outline-light">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>


            </div>
        </div>
    </nav>

    <!-- Slider 3 image -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://fastly.picsum.photos/id/7/4728/3168.jpg?hmac=c5B5tfYFM9blHHMhuu4UKmhnbZoJqrzNOP9xjkV4w3o"
                    class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Selamat Datang di Perpustakaan Digital</h5>
                    <p>Temukan berbagai koleksi buku terbaik untuk Anda!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://fastly.picsum.photos/id/8/5000/3333.jpg?hmac=OeG5ufhPYQBd6Rx1TAldAuF92lhCzAhKQKttGfawWuA"
                    class="d-block w-100" alt="Banner 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Koleksi Buku Lengkap</h5>
                    <p>Jelajahi buku dari berbagai kategori dan topik menarik.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://fastly.picsum.photos/id/1/5000/3333.jpg?hmac=Asv2DU3rA_5D1xSe22xZK47WEAN0wjWeFOhzd13ujW4"
                    class="d-block w-100" alt="Banner 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Akses Buku Kapan Saja</h5>
                    <p>Baca buku favorit Anda secara online dari mana saja.</p>
                </div>
            </div>
        </div>

        <!-- Tombol Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Bagian Layanan -->
    <section id="tentang" class="container my-5" data-aos="fade-up">
        <h2 class="text-center mb-4">Layanan Perpustakaan</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/128/2331/2331970.png" alt="Peminjaman Buku"
                                width="60">
                        </div>
                        <h5 class="card-title">Peminjaman Buku</h5>
                        <p class="card-text">Pinjam buku dengan mudah secara online tanpa harus datang langsung.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/128/1828/1828724.png" alt="E-Book Gratis"
                                width="60">
                        </div>
                        <h5 class="card-title">E-Book Gratis</h5>
                        <p class="card-text">Akses koleksi e-book gratis kapan saja untuk menemani bacaan Anda.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/128/1084/1084625.png" alt="Ruang Baca Digital"
                                width="60">
                        </div>
                        <h5 class="card-title">Ruang Baca Digital</h5>
                        <p class="card-text">Nikmati ruang baca virtual dengan berbagai fitur canggih dan rasakan
                            fiturnya.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <img src="https://cdn-icons-png.flaticon.com/128/535/535188.png" alt="Forum Diskusi"
                                width="60">
                        </div>
                        <h5 class="card-title">Forum Diskusi</h5>
                        <p class="card-text">Bergabunglah dengan komunitas pembaca dan diskusikan buku favorit Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profil Perpustakaan -->
    <section class="bg-light-gray" data-aos="fade-up">
        <div class="container my-5">
            <div class="row align-items-center">
                <!-- Video YouTube -->
                <div class="col-md-6">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/2GbuclMn6TI" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Deskripsi Profil -->
                <div class="col-md-6">
                    <h2>Tentang Perpustakaan</h2>
                    <p>
                        Perpustakaan Digital kami menyediakan ribuan koleksi buku yang dapat diakses secara online.
                        Dengan berbagai layanan modern, kami berkomitmen untuk meningkatkan literasi dan memudahkan
                        masyarakat dalam mendapatkan ilmu.
                    </p>
                    <p>
                        Kami juga menyediakan forum diskusi, ruang baca digital, dan akses gratis ke berbagai e-book.
                        Bergabunglah dan temukan dunia baru melalui buku!
                    </p>
                    <a href="#" class="btn btn-primary">Selengkapnya</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Koleksi Buku -->
    <section id="koleksi" class="container my-5" data-aos="fade-up">
        <h2 class="text-center mb-4">📚 Koleksi Buku</h2>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4">
                        <div class="card buku h-100 shadow-sm border-0">
                            <img src="<?= htmlspecialchars($book['cover_image'] ?: 'https://via.placeholder.com/300x400?text=No+Image') ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($book['title']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-primary">Rp <?= number_format($book['price'], 0, ',', '.') ?></span>
                                    <form method="POST" action="add_to_cart.php" class="m-0">
                                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-cart-plus"></i> Tambah
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted">Belum ada buku tersedia.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Bagian Kontak -->
    <section id="kontak" class="bg-light-gray" data-aos="fade-up">
        <div class="container my-5">
            <h2 class="text-center mb-4">📩 Hubungi Kami</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow p-4">
                        <form>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" placeholder="Masukkan Nama Anda"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Masukkan Email Anda"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="pesan" class="form-label">Pesan</label>
                                <textarea class="form-control" id="pesan" rows="4" placeholder="Tulis pesan Anda..."
                                    required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <!-- Tentang Perpustakaan -->
                <div class="col-md-4">
                    <h5>📚 Tentang Kami</h5>
                    <p>Perpustakaan Digital menyediakan akses ke ribuan buku berkualitas. Temukan ilmu dan inspirasi di
                        sini.</p>
                </div>

                <!-- Navigasi Cepat -->
                <div class="col-md-4">
                    <h5>🔗 Navigasi</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Beranda</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Koleksi Buku</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Layanan</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Kontak</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div class="col-md-4">
                    <h5>📞 Kontak</h5>
                    <p>Email: info@perpustakaan.com</p>
                    <p>Telepon: +62 812-3456-7890</p>
                    <div>
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-4">
                <p class="mb-0">&copy; 2025 Perpustakaan Digital. All Rights Reserved.</p>
            </div>
        </div>
    </footer>



    <!-- Modal (Popup) -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Detail Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan klik tombol di bawah ini untuk membaca buku dalam format PDF.</p>
                    <a id="pdfLink" href="#" target="_blank" class="btn btn-primary">Buka PDF</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Alert -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-danger text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">⚠️ Peringatan!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Buku tidak ditemukan! Coba judul lain.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6281234567890" class="wa-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openPDF(pdfUrl) {
        document.getElementById('pdfLink').href = pdfUrl;
        var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
        pdfModal.show();
    }
</script>
<!-- Script AOS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // Durasi animasi dalam milidetik (1000ms = 1 detik)
        once: true, // Animasi hanya terjadi sekali saat scroll ke bawah
    });
</script>

<script>
    function searchBook() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let books = document.querySelectorAll(".buku");
        //console.log(books);

        let found = false;

        books.forEach((book) => {
            let title = book.querySelector(".card-title").innerText.toLowerCase();
            console.log(title);
            if (title.includes(input)) {
                book.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                book.classList.add("highlight"); // Tambahkan efek highlight
                setTimeout(() => book.classList.remove("highlight"), 2000);
                found = true;
            }
        });
        console.log(found);

        if (!found) {
            let myModal = new bootstrap.Modal(document.getElementById('alertModal'));
            myModal.show(); // Menampilkan modal jika buku tidak ditemukan
        }
    }
</script>


</html>