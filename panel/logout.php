<?php
include '../koneksi.php';

session_destroy();
echo "<script>alert('Logout Berhasil'); window.location = '../login.php';</script>";
