<?php
include '../koneksi.php';
$id = $_GET['id'];

$row = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_keluar WHERE id_barang_keluar = '$id'"));
$jml = $row['jml_keluar'];
$id_barang = $row['id_barang'];

// Kembalikan stok sebelum hapus 
mysqli_query($koneksi, "UPDATE barang SET stock = stock + $jml WHERE id_barang = '$id_barang'");

// Hapus data
$hapus = mysqli_query($koneksi, "DELETE FROM barang_keluar WHERE id_barang_keluar = '$id'");
if ($hapus) {
    echo "<script>alert('Berhasil dihapus!'); window.location='index.php?page=bahankeluar';</script>";
}
