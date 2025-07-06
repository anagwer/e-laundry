<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Laundry | Register</title>
    <!-- Bootstrap CSS -->
    <link href="assets/admin/assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="assets/admin/assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Themify Icons -->
    <link href="assets/admin/assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- Theme Styles -->
    <link href="assets/admin/assets/css/main.css" rel="stylesheet" />
    <!-- Page Level Styles -->
    <link href="assets/admin/assets/css/pages/auth-light.css" rel="stylesheet" />

    <style>
        button {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Daftar Akun Baru</h4>

                        <form id="register-form" method="post" novalidate>
                            <!-- Baris 1: Nama dan Tanggal Daftar -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" autocomplete="off" required />
                                    <div class="invalid-feedback">Nama wajib diisi.</div>
                                </div>
                                <div class="col-6">
                                    <label for="tgl_daftar" class="form-label">Tanggal Daftar</label>
                                    <input type="date" class="form-control" id="tgl_daftar" name="tgl_daftar" required value="<?= date('Y-m-d') ?>" />
                                    <div class="invalid-feedback">Tanggal daftar wajib diisi.</div>
                                </div>
                            </div>

                            <!-- Baris 2: Username dan Status Akses -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" autocomplete="off" required />
                                    <div class="invalid-feedback">Username wajib diisi.</div>
                                </div>
                                <div class="col-6">
                                    <label for="level_akses" class="form-label">Status Akses</label>
                                    <select class="form-control" id="level_akses" name="level_akses" required>
                                        <option value="" selected disabled>Pilih Status</option>
                                        <option value="Pelanggan">Pelanggan</option>
                                    </select>
                                    <div class="invalid-feedback">Status akses wajib dipilih.</div>
                                </div>
                            </div>

                            <!-- Baris 3: Password dan Konfirmasi Password -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required />
                                    <div class="invalid-feedback">Password wajib diisi.</div>
                                </div>
                                <div class="col-6">
                                    <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required />
                                    <div class="invalid-feedback" id="confirm-error">Konfirmasi password wajib diisi dan harus sama dengan password.</div>
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>

                            <div class="mt-3 text-center">
                                Sudah punya akun? <a href="login.php">Login</a>
                            </div>
                        </form>

                        <?php
                        if (isset($_POST['register'])) {
                            $nama = trim($_POST['nama']);
                            $tgl_daftar = $_POST['tgl_daftar'];
                            $username = trim($_POST['username']);
                            $level_akses = $_POST['level_akses'];
                            $password = $_POST['password'];
                            $password_confirm = $_POST['password_confirm'];

                            // Validasi password sama
                            if ($password !== $password_confirm) {
                                echo '<div class="alert alert-danger mt-3">Password dan konfirmasi password tidak cocok.</div>';
                            } else {
                                // Cek username sudah ada atau belum
                                $cekUser = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
                                if (mysqli_num_rows($cekUser) > 0) {
                                    echo '<div class="alert alert-danger mt-3">Username sudah digunakan, silakan pilih yang lain.</div>';
                                } else {
                                    // Hash password
                                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                                    // Simpan data ke database (pastikan kolom nama, tgl_daftar, level_akses ada di tabel user)
                                    $insert = mysqli_query($koneksi, "INSERT INTO user (nm_user, tgl_daftar, username, level_akses, password) VALUES ('$nama', '$tgl_daftar', '$username', '$level_akses', '$hashed_password')");
                                    if ($insert) {
                                        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location = 'login.php';</script>";
                                    } else {
                                        echo '<div class="alert alert-danger mt-3">Terjadi kesalahan saat registrasi. Coba lagi.</div>';
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            'use strict'

            const form = document.getElementById('register-form')
            const password = document.getElementById('password')
            const passwordConfirm = document.getElementById('password_confirm')
            const confirmError = document.getElementById('confirm-error')

            form.addEventListener('submit', function(event) {
                passwordConfirm.setCustomValidity('')
                if (passwordConfirm.value !== password.value) {
                    passwordConfirm.setCustomValidity('Passwords do not match')
                }

                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            })
        })()
    </script>

    <script src="assets/admin/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>