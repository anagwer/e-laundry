<?php
session_start();

date_default_timezone_set('Asia/Jakarta');

$username = "root";
$password = "";
$server = "localhost";
$database = "e_laundry";

$koneksi = mysqli_connect($server, $username, $password, $database);

if (mysqli_connect_errno()) {
    echo "koneksi database gagal : " . mysqli_connect_error();
}
