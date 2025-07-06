<?php
include '../koneksi.php';

$id = $_GET['id'];
$hapus = mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan = '$id'");
if ($hapus) {
    echo "<script>alert('Berhasil dihapus!'); window.location='index.php?page=pelanggan';</script>";
} else {
    echo "<script>alert('Gagal dihapus!'); window.location='index.php?page=pelanggan';</script>";
}
