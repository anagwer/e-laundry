<?php
$id = $_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id'"));

if (isset($_POST['update'])) {
    $nama = $_POST['nm_user'];
    $username = $_POST['username'];
    $level = $_POST['level_akses'];
    $id_pelanggan = isset($_POST['id_pelanggan']) ? $_POST['id_pelanggan'] : '';

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE user SET password = '$password' WHERE id_user = '$id'");
    }

    mysqli_query($koneksi, "UPDATE user SET nm_user = '$nama', username = '$username', level_akses = '$level' WHERE id_user = '$id'");

    // Reset semua pelanggan yang sebelumnya mungkin terkait user ini
    mysqli_query($koneksi, "UPDATE pelanggan SET id_user = 0 WHERE id_user = '$id'");

    // Jika level Pelanggan dan pelanggan dipilih
    if ($level == 'Pelanggan' && $id_pelanggan != '') {
        mysqli_query($koneksi, "UPDATE pelanggan SET id_user = '$id' WHERE id_pelanggan = '$id_pelanggan'");
    }

    echo "<script>alert('Data berhasil diperbarui'); window.location='index.php?page=user';</script>";
}
?>

<div class="page-heading">
    <h1 class="page-title">Edit User</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=user">User</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit User</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nm_user" class="form-control" value="<?= htmlspecialchars($user['nm_user']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password <small>(Kosongkan jika tidak diubah)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Level Akses</label>
                            <select name="level_akses" id="level_akses" class="form-control" required>
                                <option value="Admin" <?= $user['level_akses'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <!-- <option value="Pelanggan" <?= $user['level_akses'] == 'Pelanggan' ? 'selected' : '' ?>>Pelanggan</option> -->
                            </select>
                        </div>
                        <div class="form-group" id="pelanggan-section" style="display: none;">
                            <label>Pilih Pelanggan</label>
                            <select name="id_pelanggan" class="form-control">
                                <option value="">-- Belum ada data pelanggan --</option>
                                <?php
                                $pelanggan = mysqli_query($koneksi, "
                                    SELECT * FROM pelanggan 
                                    WHERE id_user = 0 OR id_user = '$id' 
                                    ORDER BY nm_pelanggan ASC
                                ");
                                while ($p = mysqli_fetch_assoc($pelanggan)) {
                                    $selected = ($p['id_user'] == $id) ? 'selected' : '';
                                    echo "<option value='{$p['id_pelanggan']}' $selected>{$p['nm_pelanggan']} - {$p['no_telp']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=user" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePelangganSection() {
        const selected = document.getElementById("level_akses").value;
        const section = document.getElementById("pelanggan-section");
        section.style.display = selected === "Pelanggan" ? "block" : "none";
    }

    document.getElementById("level_akses").addEventListener("change", togglePelangganSection);
    window.addEventListener("DOMContentLoaded", togglePelangganSection);
</script>