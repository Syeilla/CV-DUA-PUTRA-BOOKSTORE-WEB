<?php
session_start();
include('config.php'); // file untuk konfigurasi database

// Cek apakah user sudah login
$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toko Buku</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>CV DUA PUTRA</h1>
        <nav>
            <ul>
                <li class="active"><a href="index.php">Rekomendasi Buku</a></li>
                <?php if ($logged_in): ?>
                    <li class="active"><a href="cart.php">Keranjang</a></li>
                    <li class="active"><a href="payment.php">Pembayaran</a></li>
                    <li class="active"><a href="history.php">Riwayat</a></li>
                    <li class="active"><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="active"><a href="login.php">Login</a></li>
                    <li class="active"><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <section id="recommendations">
            <h2>Rekomendasi Buku</h2><br>
            <div class="book-list">
                <?php
                // Ambil data buku unggulan dari database
                $query = "SELECT * FROM books LIMIT 50";
                $result = mysqli_query($conn, $query);

                while ($book = mysqli_fetch_assoc($result)): ?>
                    <div class="book-item">
                        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                        <h3><?php echo $book['title']; ?></h3>
                        <p>Stok: <?php echo $book['stock']; ?></p>
                        <p>Harga: Rp <?php echo $book['price']; ?></p>
                        <a href="cart.php?add_to_cart=<?php echo $book['id']; ?>" class="cart-button">Tambah ke Keranjang</a>
                        <a href="details.php?id=<?php echo $book['id']; ?>" class="detail-link">Detail Buku</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>
    <script src="script.js"></script>
</body>
</html>
