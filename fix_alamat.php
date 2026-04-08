<?php
include 'config/database.php';

$query = "ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Kolom alamat berhasil ditambahkan ke tabel users.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>