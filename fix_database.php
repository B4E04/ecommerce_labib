<?php
include 'config/database.php';

$query = "ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "<h2>Kolom alamat berhasil ditambahkan ke tabel users.</h2>";
    echo "<p>Sekarang Anda bisa menggunakan fitur profil dan checkout.</p>";
} else {
    echo "<h2>Error:</h2> <p>" . mysqli_error($conn) . "</p>";
}
?>