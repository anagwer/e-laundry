<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Laundry | Login</title>
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
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <!-- <div
                                class="border border-secondary rounded d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 60px; background-color: #f9f9f9; font-size: 12px; color: #666;">
                                Gambar
                            </div> -->
                            <img src="assets/uploads/logo.png" alt="" width="100">
                        </div>

                        <form id="login-form" method="post" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    autocomplete="off"
                                    required />
                                <div class="invalid-feedback">Username wajib diisi.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required />
                                <div class="invalid-feedback">Password wajib diisi.</div>
                            </div>

                            <div class="form-check mb-4">
                                <label class="form-check-label" for="remember">Belum Punya Akun? <a href="register.php"> Daftar</a></label>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
        $user = mysqli_fetch_array($query);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                echo "<script>alert('Login Berhasil'); window.location = 'panel/index.php';</script>";
            } else {
                echo "<script>alert('Password Salah'); window.location = 'login.php';</script>";
            }
        } else {
            echo "<script>alert('Username Tidak Ditemukan'); window.location = 'login.php';</script>";
        }
    }
    ?>

    <script src="assets/admin/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>