<?php
$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM kategori_layanan WHERE id_kategori_layanan = '$id'");
$row = mysqli_fetch_assoc($data);
?>

<div class="page-heading">
    <h1 class="page-title">Edit Kategori Layanan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item"><a href="index.php?page=layanan">Kategori Layanan</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-3">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit Layanan</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="nm_layanan">Nama Layanan</label>
                            <input type="text" class="form-control" name="nm_layanan" id="nm_layanan" value="<?= htmlspecialchars($row['nm_layanan']); ?>" required autofocus>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=layanan" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <?php
                    if (isset($_POST['update'])) {
                        $nama = trim($_POST['nm_layanan']);
                        if ($nama != '') {
                            $cek = mysqli_query($koneksi, "SELECT * FROM kategori_layanan WHERE nm_layanan = '$nama' AND id_kategori_layanan != '$id'");
                            if (mysqli_num_rows($cek) > 0) {
                                echo '<div class="alert alert-warning mt-3">Nama layanan sudah ada!</div>';
                            } else {
                                $update = mysqli_query($koneksi, "UPDATE kategori_layanan SET nm_layanan = '$nama' WHERE id_kategori_layanan = '$id'");
                                if ($update) {
                                    echo "<script>alert('Berhasil diupdate!'); window.location='index.php?page=layanan';</script>";
                                } else {
                                    echo '<div class="alert alert-danger mt-3">Gagal mengupdate data.</div>';
                                }
                            }
                        } else {
                            echo '<div class="alert alert-warning mt-3">Nama layanan tidak boleh kosong.</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>