<?php
$id = $_SESSION['user']['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id'"));
$pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_user = '$id'"));

if (isset($_POST['update'])) {
    $nama = $_POST['nm_user'];
    $username = $_POST['username'];
    $id_pelanggan = $_POST['id_pelanggan'] ?? '';

    // Update password jika diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE user SET password = '$password' WHERE id_user = '$id'");
    }

    // Update user
    mysqli_query($koneksi, "UPDATE user SET nm_user = '$nama', username = '$username' WHERE id_user = '$id'");

    // Jika pelanggan dipilih dari dropdown
    if ($id_pelanggan != '') {
        // Reset semua pelanggan yang terkait user ini
        mysqli_query($koneksi, "UPDATE pelanggan SET id_user = 0 WHERE id_user = '$id'");
        // Hubungkan yang baru dipilih
        mysqli_query($koneksi, "UPDATE pelanggan SET id_user = '$id' WHERE id_pelanggan = '$id_pelanggan'");
    }
    // Jika pelanggan belum ada dan ingin menambahkan baru
    elseif (isset($_POST['nm_pelanggan'])) {
        $nm_pelanggan = $_POST['nm_pelanggan'];
        $no_telp = $_POST['no_telp'];
        $kecamatan = $_POST['kecamatan'];
        $alamat = $_POST['alamat'];
        $tgl_daftar = date('Y-m-d');

        mysqli_query($koneksi, "INSERT INTO pelanggan (id_user, nm_pelanggan, no_telp, kecamatan, alamat, tgl_daftar) 
            VALUES ('$id', '$nm_pelanggan', '$no_telp', '$kecamatan', '$alamat', '$tgl_daftar')");
    }

    echo "<script>alert('Profil berhasil diperbarui'); window.location='index.php?page=profile';</script>";
}
?>

<div class="page-heading">
    <h1 class="page-title">Profil Saya</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item active">Profil</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Profil</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <h5 class="mb-3 text-primary">Informasi Akun</h5>
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
                            <input type="text" class="form-control" value="<?= $user['level_akses'] ?>" readonly>
                        </div>

                        <?php if ($_SESSION['user']['level_akses'] == 'Pelanggan'): ?>
                            <?php if (!$pelanggan): ?>
                                <hr>
                                <h5 class="mb-3 text-primary">Pilih Pelanggan</h5>
                                <div class="form-group">
                                    <select name="id_pelanggan" id="id_pelanggan" class="form-control">
                                        <option value="">-- Pilih dari daftar pelanggan --</option>
                                        <option value="">Belum Ada Data Pelanggan</option>
                                        <?php
                                        $q = mysqli_query($koneksi, "
                                        SELECT * FROM pelanggan 
                                        WHERE id_user = 0 OR id_user = '$id'
                                        ORDER BY nm_pelanggan ASC
                                    ");
                                        while ($p = mysqli_fetch_assoc($q)) {
                                            echo "<option value='{$p['id_pelanggan']}'>{$p['nm_pelanggan']} - {$p['no_telp']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div id="form-tambah-pelanggan" style="display: none;">
                                    <hr>
                                    <h5 class="mb-3 text-primary">Atau Tambahkan Data Pelanggan Baru</h5>
                                    <div class="form-group">
                                        <label>Nama Pelanggan</label>
                                        <input type="text" name="nm_pelanggan" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>No. Telepon</label>
                                        <input type="text" name="no_telp" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <select name="kecamatan" id="kecamatan" class="form-control">
                                            <option value="">-- Pilih Kecamatan --</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            <?php else: ?>
                                <hr>
                                <h5 class="mb-3 text-primary">Data Pelanggan</h5>
                                <div class="form-group">
                                    <label>Nama Pelanggan</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['nm_pelanggan']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['no_telp']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Kecamatan</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($pelanggan['kecamatan']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($pelanggan['alamat']) ?></textarea>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="form-group text-right">
                            <a href="index.php?page=dashboard" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Wilayah + Form Toggle -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Load kecamatan dari API EMSIFA
        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/3174.json", function(data) {
            var $kecamatanSelect = $("#kecamatan");
            $.each(data, function(index, kecamatan) {
                $kecamatanSelect.append(
                    $("<option></option>").val(kecamatan.name).text(kecamatan.name)
                );
            });
        });

        // Toggle tampil form tambah pelanggan jika tidak memilih dari dropdown
        $("#id_pelanggan").change(function() {
            const pilih = $(this).val();
            if (pilih === "") {
                $("#form-tambah-pelanggan").slideDown();
            } else {
                $("#form-tambah-pelanggan").slideUp();
            }
        });
    });
</script>