<div class="page-heading">
    <h1 class="page-title">Tambah Bahan Keluar</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=bahankeluar">Bahan Keluar</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<?php
if (isset($_POST['simpan'])) {
    $id_barang = $_POST['id_barang'];
    $tgl_keluar = $_POST['tgl_keluar'];
    $jml_keluar = $_POST['jml_keluar'];

    // Cek stock cukup
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stock FROM barang WHERE id_barang = '$id_barang'"));
    if ($cek['stock'] < $jml_keluar) {
        echo "<div class='alert alert-danger mt-3'>Stok tidak mencukupi!</div>";
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO barang_keluar (id_barang, tgl_keluar, jml_keluar) VALUES ('$id_barang', '$tgl_keluar', '$jml_keluar')");
        if ($simpan) {
            mysqli_query($koneksi, "UPDATE barang SET stock = stock - $jml_keluar WHERE id_barang = '$id_barang'");
            echo "<script>alert('Data berhasil disimpan'); window.location='index.php?page=bahankeluar';</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Gagal menyimpan data.</div>";
        }
    }
}
?>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah Bahan Keluar</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select name="id_barang" class="form-control" required>
                                <option value="" selected disabled>-- Pilih Barang --</option>
                                <?php
                                $barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nm_barang ASC");
                                while ($b = mysqli_fetch_assoc($barang)) {
                                ?>
                                    <option value="<?= $b['id_barang'] ?>"><?= $b['nm_barang'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Keluar</label>
                            <input type="date" name="tgl_keluar" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Keluar</label>
                            <input type="number" name="jml_keluar" class="form-control" required min="1">
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=bahankeluar" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>