<?php
include '../koneksi.php';

$id = $_GET['id'];
$row = mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id_barang_masuk = '$id'");
$data = mysqli_fetch_assoc($row);

$jml = $data['jml_masuk'];
$id_barang = $data['id_barang'];

// Kurangi stok barang
$update = mysqli_query($koneksi, "UPDATE barang SET stock = stock - $jml WHERE id_barang = '$id_barang'");

$query = mysqli_query($koneksi, "SELECT stock FROM barang WHERE id_barang = '$id_barang'");
$stock = mysqli_fetch_assoc($query);

if ($stock['stock'] < 0) {
    $update = mysqli_query($koneksi, "UPDATE barang SET stock = 0 WHERE id_barang = '$id_barang'");
}

// Hapus data barang masuk
$hapus = mysqli_query($koneksi, "DELETE FROM barang_masuk WHERE id_barang_masuk = '$id'");

if ($hapus) {
    echo "<script>alert('Berhasil dihapus!'); window.location='index.php?page=bahanmasuk';</script>";
}
