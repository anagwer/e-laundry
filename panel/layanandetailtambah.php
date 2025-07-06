<?php
$id_kategori = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nm_layanan FROM kategori_layanan WHERE id_kategori_layanan = '$id_kategori'"));
?>

<div class="page-heading">
    <h1 class="page-title">Tambah Jenis Layanan untuk: <?= htmlspecialchars($data['nm_layanan']); ?></h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=layanan">Kategori Layanan</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=layanandetail&id=<?= $id_kategori ?>">Detail</a></li>
        <li class="breadcrumb-item active">Tambah Jenis</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Form Tambah Jenis Layanan</div>
        </div>
        <div class="ibox-body">
            <form method="post">
                <div class="form-group">
                    <label>Jenis Layanan</label>
                    <input type="text" name="jenis_layanan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Estimasi Waktu (Hari)</label>
                    <input type="number" name="estimasi_waktu" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tarif Perkilo (Rp)</label>
                    <input type="number" name="tarif" class="form-control" required>
                </div>
                <div class="form-group text-right">
                    <a href="index.php?page=layanandetail&id=<?= $id_kategori ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                </div>
            </form>

            <?php
            if (isset($_POST['simpan'])) {
                $jenis = trim($_POST['jenis_layanan']);
                $estimasi = trim($_POST['estimasi_waktu']);
                $tarif = trim($_POST['tarif']);

                if ($jenis && $estimasi && $tarif) {
                    $simpan = mysqli_query($koneksi, "INSERT INTO jenis_layanan (id_kategori_layanan, jenis_layanan, estimasi_waktu, tarif) VALUES ('$id_kategori', '$jenis', '$estimasi', '$tarif')");
                    if ($simpan) {
                        echo "<script>alert('Data berhasil ditambahkan'); location.href='index.php?page=layanandetail&id=$id_kategori';</script>";
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Gagal menyimpan data.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning mt-3'>Semua field wajib diisi.</div>";
                }
            }
            ?>
        </div>
    </div>
</div>