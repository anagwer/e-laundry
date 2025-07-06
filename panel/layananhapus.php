<?php
include '../koneksi.php';

$id = $_GET['id'];
$hapus = mysqli_query($koneksi, "DELETE FROM kategori_layanan WHERE id_kategori_layanan = '$id'");
if ($hapus) {
    echo "<script>alert('Berhasil dihapus!'); window.location='index.php?page=layanan';</script>";
} else {
    echo "<script>alert('Gagal dihapus!'); window.location='index.php?page=layanan';</script>";
}
