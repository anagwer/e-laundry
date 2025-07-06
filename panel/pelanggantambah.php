<?php
if (isset($_POST['simpan'])) {
    $nama = $_POST['nm_pelanggan'];
    $telp = $_POST['no_telp'];
    $kecamatan = $_POST['kecamatan'];
    $alamat = $_POST['alamat'];

    $iduser = $_SESSION['user']['id_user'];

    if ($_SESSION['user']['level_akses'] == 'Admin') {
        $simpan = mysqli_query($koneksi, "INSERT INTO pelanggan (id_user, nm_pelanggan, no_telp, kecamatan, alamat) VALUES (0, '$nama', '$telp', '$kecamatan', '$alamat')");
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO pelanggan (id_user, nm_pelanggan, no_telp, kecamatan, alamat) VALUES ('$iduser', '$nama', '$telp', '$kecamatan', '$alamat')");
    }

    if ($simpan) {
        echo "<script>alert('Data berhasil disimpan'); window.location='index.php?page=pelanggan';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
    }
}
?>

<div class="page-heading">
    <h1 class="page-title">Tambah Pelanggan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=pelanggan">Pelanggan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah Pelanggan</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Pelanggan</label>
                            <input type="text" name="nm_pelanggan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan (Jakarta Selatan)</label>
                            <select name="kecamatan" id="kecamatan" class="form-control" required>
                                <option value="" selected disabled>-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=pelanggan" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/3174.json", function(data) {
            var $kecamatanSelect = $("#kecamatan");
            $.each(data, function(index, kecamatan) {
                $kecamatanSelect.append($("<option></option>").val(kecamatan.name).text(kecamatan.name));
            });
        });
    });
</script>