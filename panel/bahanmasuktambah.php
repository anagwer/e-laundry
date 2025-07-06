<div class="page-heading">
    <h1 class="page-title">Tambah Bahan Masuk</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=bahanmasuk">Bahan Masuk</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<?php
if (isset($_POST['simpan'])) {
    $id_barang = $_POST['id_barang'];
    $tgl_masuk = $_POST['tgl_masuk'];
    $jml_masuk = $_POST['jml_masuk'];

    $simpan = mysqli_query($koneksi, "INSERT INTO barang_masuk (id_barang, tgl_masuk, jml_masuk) VALUES ('$id_barang', '$tgl_masuk', '$jml_masuk')");

    if ($simpan) {
        mysqli_query($koneksi, "UPDATE barang SET stock = stock + $jml_masuk WHERE id_barang = '$id_barang'");
        echo "<script>alert('Data berhasil disimpan')</script>";
        echo "<script>window.location='index.php?page=bahanmasuk';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Gagal menyimpan data.</div>";
    }
}
?>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah Bahan Masuk</div>
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
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Masuk</label>
                            <input type="number" name="jml_masuk" class="form-control" required min="1">
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=bahanmasuk" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>