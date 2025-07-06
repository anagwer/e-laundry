<?php
include "koneksi.php";

if (isset($_SESSION['user'])) {
    echo "<script>
        window.location.href = 'panel/index.php';
    </script>";
} else {
    echo "<script>
        window.location.href = 'login.php';
    </script>";
}
