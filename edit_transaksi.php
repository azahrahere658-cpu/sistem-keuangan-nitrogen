<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/database.php";

$id = intval($_GET['id'] ?? 0);
$success = "";
$error = "";

// Ambil data lama
$q = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    header("Location: laporan.php");
    exit();
}

// Proses Update
if (isset($_POST['update'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);

    // Khusus pemasukan ada metode
    $metode_sql = "";
    if ($data['jenis'] == 'pemasukan') {
        $metode = mysqli_real_escape_string($conn, $_POST['metode']);
        $metode_sql = ", metode='$metode'";
    }

    if (!empty($tanggal) && !empty($keterangan) && !empty($jumlah)) {
        $query = "UPDATE transaksi SET tanggal='$tanggal', keterangan='$keterangan', jumlah='$jumlah' $metode_sql WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            $success = "Transaksi berhasil diperbarui! <a href='laporan.php'>Kembali ke Laporan</a>";
            // Refresh data lama
            $q = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id");
            $data = mysqli_fetch_assoc($q);
        } else {
            $error = "Gagal mengupdate: " . mysqli_error($conn);
        }
    } else {
        $error = "Semua kolom wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi - Nitrogen</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; color: #222; }
        .card { background: white; padding: 30px; max-width: 500px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 25px; font-size: 22px; color: #C62828; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 7px; color: #444; }
        input, select { width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        button { background: #C62828; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
        button:hover { background: #A61B1B; }
        .alert { padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .alert-error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .alert-success a { color: #2e7d32; font-weight: bold; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h2>Edit Transaksi</h2>
    <?php if ($success): ?><div class="alert alert-success"><?= $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= $error; ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Jenis Transaksi</label>
            <input type="text" value="<?= ucfirst($data['jenis']); ?>" disabled style="background:#eee;">
        </div>
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" required>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" value="<?= htmlspecialchars($data['keterangan']); ?>" required>
        </div>
        <?php if ($data['jenis'] == 'pemasukan'): ?>
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="metode" required>
                <option value="Tunai" <?= $data['metode'] == 'Tunai' ? 'selected' : ''; ?>>Tunai</option>
                <option value="QRIS" <?= $data['metode'] == 'QRIS' ? 'selected' : ''; ?>>QRIS</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Jumlah (Rp)</label>
            <input type="number" name="jumlah" value="<?= $data['jumlah']; ?>" required>
        </div>
        <button type="submit" name="update">Simpan Perubahan</button>
    </form>
    <a href="laporan.php" class="back-link">Batal dan Kembali</a>
</div>
</body>
</html>
