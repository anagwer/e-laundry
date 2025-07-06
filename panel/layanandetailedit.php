<?php
$id = $_GET['id'];
$id_kategori = $_GET['id_kategori'];

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM jenis_layanan WHERE id_jns_layanan = '$id'"));
$kategori = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nm_layanan FROM kategori_layanan WHERE id_kategori_layanan = '$id_kategori'"));
?>

<div class="page-heading">
    <h1 class="page-title">Edit Jenis Layanan: <?= htmlspecialchars($kategori['nm_layanan']); ?></h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=layanan">Kategori Layanan</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=layanandetail&id=<?= $id_kategori ?>">Detail</a></li>
        <li class="breadcrumb-item active">Edit Jenis</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Form Edit Jenis Layanan</div>
        </div>
        <div class="ibox-body">
            <form method="post">
                <div class="form-group">
                    <label>Jenis Layanan</label>
                    <input type="text" name="jenis_layanan" class="form-control" value="<?= htmlspecialchars($data['jenis_layanan']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Estimasi Waktu (Hari)</label>
                    <input type="text" name="estimasi_waktu" class="form-control" value="<?= htmlspecialchars($data['estimasi_waktu']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Tarif Perkilo (Rp)</label>
                    <input type="number" name="tarif" class="form-control" value="<?= htmlspecialchars($data['tarif']) ?>" required>
                </div>
                <div class="form-group text-right">
                    <a href="index.php?page=layanandetail&id=<?= $id_kategori ?>" class="btn btn-secondary">Kembali</a>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div>
            </form>

            <?php
            if (isset($_POST['update'])) {
                $jenis = trim($_POST['jenis_layanan']);
                $estimasi = trim($_POST['estimasi_waktu']);
                $tarif = trim($_POST['tarif']);

                $update = mysqli_query($koneksi, "UPDATE jenis_layanan SET jenis_layanan = '$jenis', estimasi_waktu = '$estimasi', tarif = '$tarif' WHERE id_jns_layanan = '$id'");

                if ($update) {
                    echo "<script>alert('Data berhasil diperbarui'); location.href='index.php?page=layanandetail&id=$id_kategori';</script>";
                } else {
                    echo "<div class='alert alert-danger mt-3'>Gagal mengupdate data.</div>";
                }
            }
            ?>
        </div>
    </div>
</div>