<?php
session_start();
include('config.php'); // file untuk konfigurasi database

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil detail buku dari keranjang
$cart_books = array();
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $cart_ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM books WHERE id IN ($cart_ids)";
    $result = mysqli_query($conn, $query);
    while ($book = mysqli_fetch_assoc($result)) {
        $book['quantity'] = $_SESSION['cart'][$book['id']];
        $book['subtotal'] = $book['price'] * $book['quantity'];
        $total_price += $book['subtotal'];
        $cart_books[] = $book;
    }
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $address = $_POST['address'];
    $total = $total_price;
    $status = 'Pending';

    $query = "INSERT INTO orders (user_id, total_price, address, status) VALUES ('$user_id', '$total', '$address', '$status')";
    mysqli_query($conn, $query);
    $order_id = mysqli_insert_id($conn);

    foreach ($cart_books as $book) {
        $book_id = $book['id'];
        $quantity = $book['quantity'];
        $price = $book['price'];
        $subtotal = $book['subtotal'];

        $query = "INSERT INTO order_items (order_id, book_id, quantity, price, subtotal) VALUES ('$order_id', '$book_id', '$quantity', '$price', '$subtotal')";
        mysqli_query($conn, $query);
    }

    unset($_SESSION['cart']);
    header('Location: history.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Pembayaran</h1>
        <nav>
            <ul>
                <li class="active"><a href="index.php">Rekomendasi Buku</a></li>
                <li class="active"><a href="cart.php">Keranjang</a></li>
                <li class="active"><a href="payment.php">Pembayaran</a></li>
                <li class="active"><a href="history.php">Riwayat</a></li>
                <li class="active"><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Rincian Pembayaran</h2>
        <?php if (!empty($cart_books)): ?>
            <ul>
                <?php foreach ($cart_books as $book): ?>
                    <li>
                        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                        <h3><?php echo $book['title']; ?></h3>
                        <p>Harga: Rp <?php echo $book['price']; ?></p>
                        <p>Jumlah: <?php echo $book['quantity']; ?></p>
                        <p>Subtotal: Rp <?php echo $book['subtotal']; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul><br>
            <h3>Total Harga: Rp <?php echo $total_price; ?></h3>
            <form method="POST"><br>
                <label for="address">Alamat Pengiriman:</label>
                <textarea id="address" name="address" required ></textarea>
                <button type="submit">Bayar</button>
            </form>
        <?php else: ?>
            <p>Rincian Pembayaran anda kosong.</p>
        <?php endif; ?><br>
        <button><a href="index.php">Kembali Ke Halaman Utama</a></button>
    </main>
    <script src="script.js"></script>
</body>
</html>
