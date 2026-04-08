<?php
include 'config/database.php';

// Drop the old foreign key
mysqli_query($conn, 'ALTER TABLE bukti_pembayaran DROP FOREIGN KEY fk_bukti_transaksi');

// Add the new foreign key to pesanan
mysqli_query($conn, 'ALTER TABLE bukti_pembayaran ADD CONSTRAINT fk_bukti_pesanan FOREIGN KEY (transaksi_id) REFERENCES pesanan(id) ON DELETE CASCADE ON UPDATE CASCADE');

echo "Foreign key updated successfully.";
?>