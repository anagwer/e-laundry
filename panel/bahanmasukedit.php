<?php
$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM barang_masuk WHERE id_barang_masuk = '$id'");
$row = mysqli_fetch_assoc($data);
?>

<div class="page-heading">
    <h1 class="page-title">Edit Bahan Masuk</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=bahanmasuk">Bahan Masuk</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>

<?php
if (isset($_POST['update'])) {
    $tgl_masuk = $_POST['tgl_masuk'];
    $jml_baru = $_POST['jml_masuk'];
    $jml_lama = $row['jml_masuk'];
    $selisih = $jml_baru - $jml_lama;

    $update = mysqli_query($koneksi, "UPDATE barang_masuk SET tgl_masuk='$tgl_masuk', jml_masuk='$jml_baru' WHERE id_barang_masuk='$id'");

    if ($update) {
        mysqli_query($koneksi, "UPDATE barang SET stock = stock + $selisih WHERE id_barang = '{$row['id_barang']}'");
        echo "<script>alert('Data berhasil diupdate')</script>";
        echo "<script>window.location='index.php?page=bahanmasuk';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Gagal mengupdate data.</div>";
    }
}
?>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit Bahan Masuk</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select name="id_barang" class="form-control" disabled>
                                <?php
                                $barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nm_barang ASC");
                                while ($b = mysqli_fetch_assoc($barang)) {
                                    $selected = $b['id_barang'] == $row['id_barang'] ? 'selected' : '';
                                ?>
                                    <option value="<?= $b['id_barang'] ?>" <?= $selected ?>><?= $b['nm_barang'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control" required value="<?= $row['tgl_masuk'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Masuk</label>
                            <input type="number" name="jml_masuk" class="form-control" required min="1" value="<?= $row['jml_masuk'] ?>">
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=bahanmasuk" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </di