<?php
include 'config/database.php';
$result = mysqli_query($conn, 'ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL');
if ($result) {
    echo 'Kolom alamat berhasil ditambahkan.';
} else {
    echo 'Error: ' . mysqli_error($conn);
}
?>