<?php
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang_keluar WHERE id_barang_keluar = '$id'"));

if (isset($_POST['update'])) {
    $id_barang_baru = $_POST['id_barang'];
    $tgl_keluar = $_POST['tgl_keluar'];
    $jml_baru = $_POST['jml_keluar'];

    $id_barang_lama = $data['id_barang'];
    $jml_lama = $data['jml_keluar'];

    // Rollback stok lama (kembalikan stok lama ke barang asal)
    mysqli_query($koneksi, "UPDATE barang SET stock = stock + $jml_lama WHERE id_barang = '$id_barang_lama'");

    // Ambil stok terbaru barang tujuan
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stock FROM barang WHERE id_barang = '$id_barang_baru'"));

    // Cek stok cukup
    if ($cek['stock'] < $jml_baru) {
        echo "<div class='alert alert-danger mt-3'>Stok tidak mencukupi.</div>";
        // Kembalikan rollback karena gagal update
        mysqli_query($koneksi, "UPDATE barang SET stock = stock - $jml_lama WHERE id_barang = '$id_barang_lama'");
    } else {
        // Kurangi stok sesuai permintaan baru
        mysqli_query($koneksi, "UPDATE barang SET stock = stock - $jml_baru WHERE id_barang = '$id_barang_baru'");

        $update = mysqli_query($koneksi, "UPDATE barang_keluar SET id_barang = '$id_barang_baru', tgl_keluar = '$tgl_keluar', jml_keluar = '$jml_baru' WHERE id_barang_keluar = '$id'");

        if ($update) {
            echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=bahankeluar';</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Gagal mengupdate data.</div>";
            // Rollback pengurangan jika query gagal
            mysqli_query($koneksi, "UPDATE barang SET stock = stock + $jml_baru WHERE id_barang = '$id_barang_baru'");
        }
    }
}
?>


<div class="page-heading">
    <h1 class="page-title">Edit Bahan Keluar</h1>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit Bahan Keluar</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <select name="id_barang" class="form-control" required>
                                <?php
                                $barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nm_barang ASC");
                                while ($b = mysqli_fetch_assoc($barang)) {
                                    $selected = ($b['id_barang'] == $data['id_barang']) ? 'selected' : '';
                                    echo "<option value='{$b['id_barang']}' $selected>{$b['nm_barang']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Keluar</label>
                            <input type="date" name="tgl_keluar" class="form-control" required value="<?= $data['tgl_keluar'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Keluar</label>
                            <input type="number" name="jml_keluar" class="form-control" required min="1" value="<?= $data['jml_keluar'] ?>">
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=bahankeluar" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>