<?php
if (isset($_POST['simpan'])) {
    $nama = $_POST['nm_user'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level_akses'];
    $tgl_daftar = date('Y-m-d');

    $simpan = mysqli_query($koneksi, "INSERT INTO user (nm_user, username, password, level_akses, tgl_daftar) VALUES ('$nama', '$username', '$password', '$level', '$tgl_daftar')");

    if ($simpan) {
        $id_user_baru = mysqli_insert_id($koneksi);

        // Jika level Pelanggan dan pelanggan dipilih
        if ($level == 'Pelanggan' && isset($_POST['id_pelanggan']) && $_POST['id_pelanggan'] != '') {
            $id_pelanggan = $_POST['id_pelanggan'];
            mysqli_query($koneksi, "UPDATE pelanggan SET id_user = '$id_user_baru' WHERE id_pelanggan = '$id_pelanggan'");
        }

        echo "<script>alert('User berhasil ditambahkan'); window.location='index.php?page=user';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan user.</div>";
    }
}
?>

<div class="page-heading">
    <h1 class="page-title">Tambah User</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=user">User</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah User</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nm_user" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Level Akses</label>
                            <select name="level_akses" id="level_akses" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Level --</option>
                                <option value="Admin">Admin</option>
                                <!-- <option value="Pelanggan">Pelanggan</option> -->
                            </select>
                        </div>
                        <div class="form-group" id="pelanggan-section" style="display: none;">
                            <label>Pilih Pelanggan</label>
                            <select name="id_pelanggan" class="form-control">
                                <option value="">-- Belum ada data pelanggan --</option>
                                <?php
                                $pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_user = 0 ORDER BY nm_pelanggan ASC");
                                while ($p = mysqli_fetch_assoc($pelanggan)) {
                                    echo "<option value='{$p['id_pelanggan']}'>{$p['nm_pelanggan']} - {$p['no_telp']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=user" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("level_akses").addEventListener("change", function() {
        const selected = this.value;
        const section = document.getElementById("pelanggan-section");
        section.style.display = selected === "Pelanggan" ? "block" : "none";
    });
</script>