<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>E-Laundry | Login</title>
    <link href="assets/admin/assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/admin/assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="assets/admin/assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <link href="assets/admin/assets/css/main.css" rel="stylesheet" />
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
                            <img src="assets/uploads/logo.png" alt="" width="100">
                        </div>

                        <form id="login-form" method="post" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" autocomplete="off" />
                                <div class="invalid-feedback" id="username-error">Username wajib diisi.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" />
                                <div class="invalid-feedback" id="password-error">Password wajib diisi.</div>
                            </div>

                            <div class="form-check mb-4">
                                <label class="form-check-label" for="remember">Belum Punya Akun? <a href="register.php"> Daftar</a></label>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>

                        <script>
                            const form = document.getElementById('login-form');
                            const username = document.getElementById('username');
                            const password = document.getElementById('password');

                            form.addEventListener('submit', function (e) {
                                let valid = true;

                                // Reset feedback
                                username.classList.remove('is-invalid');
                                password.classList.remove('is-invalid');

                                if (username.value.trim() === '' && password.value.trim() === '') {
                                    username.classList.add('is-invalid');
                                    password.classList.add('is-invalid');
                                    document.getElementById('username-error').textContent = 'Username wajib diisi.';
                                    document.getElementById('password-error').textContent = 'Password wajib diisi.';
                                    valid = false;
                                } else if (username.value.trim() === '') {
                                    username.classList.add('is-invalid');
                                    document.getElementById('username-error').textContent = 'Username wajib diisi.';
                                    valid = false;
                                } else if (password.value.trim() === '') {
                                    password.classList.add('is-invalid');
                                    document.getElementById('password-error').textContent = 'Password wajib diisi.';
                                    valid = false;
                                }

                                if (!valid) {
                                    e.preventDefault(); // Stop form submit
                                }
                            });
                        </script>
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
                // session_start();
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
