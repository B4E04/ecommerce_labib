<?php
include 'config/database.php';
$result = mysqli_query($conn, 'SHOW CREATE TABLE bukti_pembayaran');
$row = mysqli_fetch_assoc($result);
echo $row['Create Table'];
?>