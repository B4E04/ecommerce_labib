<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    
    // Cek dan tambah kolom jika belum ada
    $check_alamat = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'alamat'");
    if (mysqli_num_rows($check_alamat) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN alamat TEXT DEFAULT NULL");
    }
    
    $check_hp = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'hp'");
    if (mysqli_num_rows($check_hp) == 0) {
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN hp VARCHAR(20) DEFAULT NULL");
    }
    
    $update_query = "UPDATE users SET alamat = '$alamat', hp = '$hp' WHERE id = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: profil.php?success=1");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | Cartix</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563EB;
            --dark: #0F172A;
            --slate: #64748B;
            --bg: #F8FAFC;
            --white: #ffffff;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg);
            min-height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: var(--white);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: var(--dark);
            margin-bottom: 10px;
        }

        .form-section {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #1D4ED8;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .success {
            background: #D1FAE5;
            color: #065F46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #FEE2E2;
            color: #DC2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fa-solid fa-user"></i> Profil Saya</h1>
            <p>Kelola informasi akun Anda</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
        <div class="success">
            <i class="fa-solid fa-check"></i> Alamat berhasil diperbarui!
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="error">
            <i class="fa-solid fa-exclamation-triangle"></i> <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="form-section">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" value="<?= htmlspecialchars($user['nama']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="hp" placeholder="Contoh: 081234567890" value="<?php 
                        // Cek apakah kolom hp ada
                        $check_hp = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'hp'");
                        if (mysqli_num_rows($check_hp) > 0) {
                            echo htmlspecialchars($user['hp'] ?? '');
                        }
                    ?>">
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" placeholder="Masukkan alamat lengkap Anda"><?php 
                        // Cek apakah kolom alamat ada
                        $check_column = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'alamat'");
                        if (mysqli_num_rows($check_column) > 0) {
                            echo htmlspecialchars($user['alamat'] ?? '');
                        }
                    ?></textarea>
                </div>

                <button type="submit">Simpan Perubahan</button>
            </form>
        </div>

        <a href="home.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>