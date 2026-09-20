<?php
session_start();

include "../config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // --- UPDATE KEAMANAN UTAMA ---
        // Gunakan password_verify untuk mengecek hash.
        // Hapus pemeriksaan '$password === $user['password']' lama.
        if (password_verify($password, $user['password'])) {

            $_SESSION['admin'] = $user['username'];

            header("Location: ../admin/dashboard.php");
            exit();

        } else {
            $error = "Username atau Password yang kamu masukkan salah.";
        }

    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Nitrogen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { min-height: 100vh; background: #f5f5f5; display: flex; align-items: center; justify-content: center; }
        .login-container { width: 900px; max-width: 92%; min-height: 540px; background: white; border-radius: 20px; overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        .left { background: #C62828; color: white; padding: 55px; display: flex; flex-direction: column; justify-content: center; }
        .logo { width: 58px; height: 58px; border-radius: 14px; background: white; color: #C62828; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: bold; margin-bottom: 25px; }
        .left h1 { font-size: 34px; letter-spacing: 1px; margin-bottom: 12px; }
        .left p { color: #ffebee; line-height: 1.7; font-size: 14px; max-width: 350px; }
        .features { margin-top: 35px; }
        .features div { margin-bottom: 15px; font-size: 14px; }
        .right { padding: 55px; display: flex; flex-direction: column; justify-content: center; }
        .right h2 { font-size: 28px; margin-bottom: 8px; color: #222; }
        .subtitle { color: #777; font-size: 14px; margin-bottom: 30px; }
        .error { background: #ffebee; color: #c62828; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; border: 1px solid #ffcdd2; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 8px; color: #333; }
        input { width: 100%; padding: 14px 15px; border: 1px solid #d5d5d5; border-radius: 9px; outline: none; font-size: 14px; }
        input:focus { border-color: #C62828; box-shadow: 0 0 0 3px #ffebee; }
        button { width: 100%; padding: 14px; border: none; border-radius: 9px; background: #C62828; color: white; font-size: 14px; font-weight: bold; cursor: pointer; margin-top: 5px; }
        button:hover { background: #A61B1B; }
        .footer { text-align: center; margin-top: 25px; color: #aaa; font-size: 11px; }
        @media (max-width: 700px) { .login-container { grid-template-columns: 1fr; } .left { display: none; } .right { padding: 40px 30px; } }
    </style>
</head>

<body>
<div class="login-container">
    <div class="left">
        <div class="logo">N</div>
        <h1>NITROGEN</h1>
        <p>Sistem Keuangan Admin untuk membantu mencatat dan mengelola transaksi keuangan bisnis nitrogen dengan lebih mudah.</p>
        <div class="features">
            <div>✓ Kelola pemasukan</div>
            <div>✓ Kelola pengeluaran</div>
            <div>✓ Pantau saldo keuangan</div>
            <div>✓ Lihat laporan transaksi</div>
        </div>
    </div>
    <div class="right">
        <h2>Selamat Datang 👋</h2>
        <p class="subtitle">Silakan masuk ke akun administrator.</p>
        <?php if ($error != ""): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login">Masuk ke Dashboard</button>
        </form>
        <div class="footer">© 2026 Sistem Keuangan Nitrogen</div>
    </div>
</div>
</body>
</html>
