<?php
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id'"));

if (isset($_POST['update'])) {
    $nama = $_POST['nm_pelanggan'];
    $telp = $_POST['no_telp'];
    $kecamatan = $_POST['kecamatan'];
    $alamat = $_POST['alamat'];

    $update = mysqli_query($koneksi, "UPDATE pelanggan SET 
        nm_pelanggan = '$nama', 
        no_telp = '$telp', 
        kecamatan = '$kecamatan', 
        alamat = '$alamat'
        WHERE id_pelanggan = '$id'");

    if ($update) {
        echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=pelanggan';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate data.</div>";
    }
}
?>

<div class="page-heading">
    <h1 class="page-title">Edit Pelanggan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=pelanggan">Pelanggan</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12 offset-md-2">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit Pelanggan</div>
                </div>
                <div class="ibox-body">
                    <form method="post">
                        <div class="form-group">
                            <label>Nama Pelanggan</label>
                            <input type="text" name="nm_pelanggan" class="form-control" required value="<?= htmlspecialchars($data['nm_pelanggan']); ?>">
                        </div>
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" required value="<?= htmlspecialchars($data['no_telp']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Kecamatan (Jakarta Selatan)</label>
                            <select name="kecamatan" id="kecamatan" class="form-control" required>
                                <option value="" disabled>-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data['alamat']); ?></textarea>
                        </div>
                        <div class="form-group text-right">
                            <a href="index.php?page=pelanggan" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + Kecamatan API -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let selectedKecamatan = "<?= $data['kecamatan'] ?>";
        $.getJSON("https://www.emsifa.com/api-wilayah-indonesia/api/districts/3174.json", function(data) {
            var $kecamatanSelect = $("#kecamatan");
            $.each(data, function(index, kecamatan) {
                let selected = (kecamatan.name === selectedKecamatan) ? "selected" : "";
                $kecamatanSelect.append(`<option value="${kecamatan.name}" ${selected}>${kecamatan.name}</option>`);
            });
        });
    });
</script>