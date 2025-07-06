<div class="page-heading">
    <h1 class="page-title">Tambah Kategori Layanan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item"><a href="index.php?page=layanan">Kategori Layanan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-3">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah Layanan</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="nm_layanan">Nama Layanan</label>
                            <input type="text" class="form-control" name="nm_layanan" id="nm_layanan" required autofocus>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=layanan" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                        </div>
                    </form>

                    <?php
                    if (isset($_POST['simpan'])) {
                        $nama = trim($_POST['nm_layanan']);
                        if ($nama != '') {
                            $cek = mysqli_query($koneksi, "SELECT * FROM kategori_layanan WHERE nm_layanan = '$nama'");
                            if (mysqli_num_rows($cek) > 0) {
                                echo '<div class="alert alert-warning mt-3">Nama layanan sudah ada!</div>';
                            } else {
                                $simpan = mysqli_query($koneksi, "INSERT INTO kategori_layanan (nm_layanan) VALUES ('$nama')");
                                if ($simpan) {
                                    echo "<script>alert('Berhasil disimpan!'); window.location='index.php?page=layanan';</script>";
                                } else {
                                    echo '<div class="alert alert-danger mt-3">Gagal menyimpan data.</div>';
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