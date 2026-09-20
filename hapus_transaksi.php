<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/database.php";

$id = $_GET['id'];

// Hapus data berdasarkan ID
mysqli_query($conn, "DELETE FROM transaksi WHERE id = '$id'");

// Balikin ke laporan.php
header("Location: laporan.php");
exit();
?>
