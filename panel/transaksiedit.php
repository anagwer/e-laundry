<?php
// Ambil data transaksi yang akan diedit
if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $query_transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'");
    $data_transaksi = mysqli_fetch_assoc($query_transaksi);

    if (!$data_transaksi) {
        echo "<script>alert('Data transaksi tidak ditemukan'); window.location='index.php?page=transaksi';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID transaksi tidak valid'); window.location='index.php?page=transaksi';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $id_transaksi = $_POST['id_transaksi'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_user = $_SESSION['user']['id_user'];
    $id_jns_layanan = $_POST['id_jns_layanan'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $antar_jemput = $_POST['antar_jemput'];
    $alamat_jemput = $_POST['alamat_jemput'];
    $berat_baru = $_POST['berat'];
    $total = $_POST['total'];
    $metode_bayar = $_POST['metode_bayar'];
    $status_bayar = $_POST['status_bayar'];
    $status_ambil = $_POST['status_ambil'];

    // Ambil data lama untuk mengembalikan stok
    $query_lama = mysqli_query($koneksi, "SELECT berat FROM transaksi WHERE id_transaksi = '$id_transaksi'");
    $data_lama = mysqli_fetch_assoc($query_lama);
    $berat_lama = $data_lama['berat'];

    // Upload bukti bayar jika ada file baru
    $bukti = $data_transaksi['bukti']; // gunakan bukti lama sebagai default
    if ($_FILES['bukti']['name'] != '') {
        // Hapus file lama jika ada
        if ($bukti && file_exists('../assets/bukti/' . $bukti)) {
            unlink('../assets/uploads/' . $bukti);
        }

        $tmp = $_FILES['bukti']['tmp_name'];
        $name = $data_transaksi['invoice'] . '-' . $_FILES['bukti']['name'];
        move_uploaded_file($tmp, '../assets/uploads/' . $name);
        $bukti = $name;
    }

    // Update transaksi
    $update = mysqli_query($koneksi, "UPDATE transaksi SET 
        id_pelanggan = '$id_pelanggan',
        id_user = '$id_user',
        id_jns_layanan = '$id_jns_layanan',
        status_bayar = '$status_bayar',
        status_ambil = '$status_ambil',
        tgl_selesai = '$tgl_selesai',
        antar_jemput = '$antar_jemput',
        alamat_jemput = '$alamat_jemput',
        berat = '$berat_baru',
        total = '$total',
        metode_bayar = '$metode_bayar',
        bukti = '$bukti'
        WHERE id_transaksi = '$id_transaksi'
    ");

    if ($status_ambil == 'Selesai') {
        mysqli_query($koneksi, "UPDATE transaksi SET tgl_diambil = NOW() WHERE id_transaksi = '$id_transaksi'");
    }

    if ($update) {
        // Ambil nama jenis layanan
        $id_jns_layanan = $data_transaksi['id_jns_layanan'];
        $q_jenis = mysqli_query($koneksi, "SELECT jenis_layanan FROM jenis_layanan WHERE id_jns_layanan = '$id_jns_layanan'");
        $data_jenis = mysqli_fetch_assoc($q_jenis);
        $jenis_layanan_nama = $data_jenis['jenis_layanan'];

        // Simpan bahan berdasarkan jenis layanan
        $bahan = [];
        if ($jenis_layanan_nama == 'Cuci + Setrika') {
            $bahan = ['Pewangi', 'Pelembut', 'Deterjen'];
        } elseif ($jenis_layanan_nama == 'Hanya Cuci') {
            $bahan = ['Pelembut', 'Deterjen'];
        } elseif ($jenis_layanan_nama == 'Setrika') {
            $bahan = ['Pewangi'];
        }

        // Kembalikan stok lama
        foreach ($bahan as $nama) {
            mysqli_query($koneksi, "UPDATE barang SET stock = stock + $berat_lama WHERE nm_barang = '$nama'");
        }
        // Kurangi stok baru
        foreach ($bahan as $nama) {
            mysqli_query($koneksi, "UPDATE barang SET stock = stock - $berat_baru WHERE nm_barang = '$nama'");
        }

        echo "<script>alert('Transaksi berhasil diupdate'); window.location='index.php?page=transaksi';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate transaksi.</div>";
    }
}

?>

<div class="page-heading">
    <h1 class="page-title">Edit Transaksi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=transaksi">Transaksi</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Edit Transaksi - Invoice: <?= $data_transaksi['invoice'] ?></div>
                </div>
                <div class="ibox-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_transaksi" value="<?= $data_transaksi['id_transaksi'] ?>">

                        <div class="row">
                            <!-- KIRI -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pelanggan</label>
                                    <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php
                                        $pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                                        while ($p = mysqli_fetch_assoc($pelanggan)) {
                                            $selected = ($p['id_pelanggan'] == $data_transaksi['id_pelanggan']) ? 'selected' : '';
                                            echo "<option value='{$p['id_pelanggan']}' data-alamat='{$p['alamat']}' data-kecamatan='{$p['kecamatan']}' $selected>{$p['nm_pelanggan']} | {$p['no_telp']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Apakah antar jemput?</label><br>
                                    <label><input type="radio" name="antar_jemput" value="Ya" <?= ($data_transaksi['antar_jemput'] == 'Ya') ? 'checked' : '' ?> required> Ya</label>
                                    <label><input type="radio" name="antar_jemput" value="Tidak" <?= ($data_transaksi['antar_jemput'] == 'Tidak') ? 'checked' : '' ?> required> Tidak</label>
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat_jemput" id="alamat_jemput" class="form-control" rows="3" required><?= $data_transaksi['alamat_jemput'] ?></textarea>
                                </div>

                                <?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>
                                    <div class="form-group">
                                        <label>Status Pembayaran</label>
                                        <select name="status_bayar" class="form-control" required>
                                            <option value="Belum" <?= ($data_transaksi['status_bayar'] == 'Belum') ? 'selected' : '' ?>>Belum</option>
                                            <option value="Sudah" <?= ($data_transaksi['status_bayar'] == 'Sudah') ? 'selected' : '' ?>>Sudah</option>
                                        </select>
                                    </div>

                                    <?php if ($data_transaksi['status_ambil'] == 'Proses'): ?>
                                        <select class="form-control" disabled>
                                            <option selected>Proses</option>
                                        </select>
                                        <input type="hidden" name="status_ambil" value="Proses">
                                    <?php else: ?>
                                        <select name="status_ambil" class="form-control" required>
                                            <option value="Proses" <?= ($data_transaksi['status_ambil'] == 'Proses') ? 'selected' : '' ?>>Proses</option>
                                            <option value="Selesai" <?= ($data_transaksi['status_ambil'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                        </select>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <input type="hidden" name="status_bayar" id="" value="<?= $data_transaksi['status_bayar']; ?>">
                                    <input type="hidden" name="status_ambil" id="" value="<?= $data_transaksi['status_ambil']; ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Total Pembayaran</label>
                                    <h3 id="total">Rp. <?= number_format($data_transaksi['total']) ?></h3>
                                    <input type="hidden" name="total" id="input_total" value="<?= $data_transaksi['total'] ?>">
                                </div>
                            </div>

                            <!-- KANAN -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kategori Layanan</label>
                                    <select id="kategori" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php
                                        $kategori = mysqli_query($koneksi, "SELECT * FROM kategori_layanan");
                                        while ($k = mysqli_fetch_assoc($kategori)) {
                                            // Cek kategori yang sedang dipilih berdasarkan jenis layanan
                                            $jenis_query = mysqli_query($koneksi, "SELECT id_kategori_layanan FROM jenis_layanan WHERE id_jns_layanan = '{$data_transaksi['id_jns_layanan']}'");
                                            $jenis_data = mysqli_fetch_assoc($jenis_query);
                                            $selected = ($k['id_kategori_layanan'] == $jenis_data['id_kategori_layanan']) ? 'selected' : '';
                                            echo "<option value='{$k['id_kategori_layanan']}' $selected>{$k['nm_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jenis Layanan</label>
                                    <select name="id_jns_layanan" id="jenis_layanan" class="form-control" required>
                                        <?php
                                        // Load jenis layanan yang sudah dipilih
                                        $jenis_layanan = mysqli_query($koneksi, "SELECT * FROM jenis_layanan WHERE id_jns_layanan = '{$data_transaksi['id_jns_layanan']}'");
                                        $jl = mysqli_fetch_assoc($jenis_layanan);
                                        echo "<option value='{$jl['id_jns_layanan']}' data-estimasi='{$jl['estimasi_waktu']}' data-tarif='{$jl['tarif']}' selected>{$jl['jenis_layanan']}</option>";
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Estimasi</label>
                                    <input type="text" id="estimasi" class="form-control" readonly value="<?= $jl['estimasi_waktu'] ?> hari">
                                </div>

                                <div class="form-group">
                                    <label>Tarif</label>
                                    <input type="text" id="tarif" class="form-control" readonly value="<?= $jl['tarif'] ?>">
                                </div>

                                <div class="form-group">
                                    <label>Berat (kg)</label>
                                    <input type="number" name="berat" id="berat" class="form-control" required min="1" value="<?= $data_transaksi['berat'] ?>">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Masuk</label>
                                    <input type="text" name="tgl_masuk" id="tgl_masuk" class="form-control" readonly value="<?= $data_transaksi['tgl_mulai'] ?>">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" value="<?= $data_transaksi['tgl_selesai'] ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Metode Pembayaran</label>
                                    <select name="metode_bayar" class="form-control" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="Transfer" <?= ($data_transaksi['metode_bayar'] == 'Transfer') ? 'selected' : '' ?>>Transfer</option>
                                        <option value="Tunai" <?= ($data_transaksi['metode_bayar'] == 'Tunai') ? 'selected' : '' ?>>Tunai</option>
                                    </select>
                                    <small>Contoh: 1237655794 (BCA | Muas Alingses)</small>
                                </div>

                                <div class="form-group">
                                    <label>Bukti Bayar</label>
                                    <?php if ($data_transaksi['bukti']): ?>
                                        <div class="mb-2">
                                            <small>File saat ini: <?= $data_transaksi['bukti'] ?></small>
                                            <!-- Tombol untuk buka modal -->
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#buktiModal">
                                                Lihat
                                            </button>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="buktiModal" tabindex="-1" role="dialog" aria-labelledby="buktiModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="buktiModalLabel">Bukti Pembayaran</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../assets/uploads/<?= $data_transaksi['bukti']; ?>" alt="Bukti Pembayaran" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <input type="file" name="bukti" class="form-control">
                                    <small>Kosongkan jika tidak ingin mengubah bukti bayar</small>
                                </div>


                                <?php
                                    // Ambil nama jenis layanan untuk logika tampilan
                                    $jenis_layanan_nama = $jl['jenis_layanan'];
                                    ?>
                                    <?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>
                                        <div class="form-group">
                                            <label>Kebutuhan Pokok (per kg)</label>
                                            <div id="kebutuhan">
                                                <?php if ($jenis_layanan_nama == 'Cuci + Setrika'): ?>
                                                    Pewangi: <span id="k_pewangi"><?= $data_transaksi['berat'] ?></span><br>
                                                    Pelembut: <span id="k_pelembut"><?= $data_transaksi['berat'] ?></span><br>
                                                    Deterjen: <span id="k_deterjen"><?= $data_transaksi['berat'] ?></span>
                                                <?php elseif ($jenis_layanan_nama == 'Hanya Cuci'): ?>
                                                    Pelembut: <span id="k_pelembut"><?= $data_transaksi['berat'] ?></span><br>
                                                    Deterjen: <span id="k_deterjen"><?= $data_transaksi['berat'] ?></span>
                                                <?php elseif ($jenis_layanan_nama == 'Setrika'): ?>
                                                    Pewangi: <span id="k_pewangi"><?= $data_transaksi['berat'] ?></span>
                                                <?php else: ?>
                                                    Tidak ada bahan diperlukan
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" name="update" class="btn btn-success">Update</button>
                            <a href="index.php?page=transaksi" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Load jenis layanan ketika kategori dipilih
        $('#kategori').change(function() {
            var kategoriId = $(this).val();
            $('#jenis_layanan').html('<option>Loading...</option>');
            $.getJSON("get_jenis_layanan.php?id=" + kategoriId, function(data) {
                var html = '<option value="">-- Pilih Jenis --</option>';
                $.each(data, function(i, item) {
                    html += `<option value="${item.id_jns_layanan}" data-estimasi="${item.estimasi_waktu}" data-tarif="${item.tarif}">${item.jenis_layanan}</option>`;
                });
                $('#jenis_layanan').html(html);
            });
        });

        // Auto isi estimasi dan tarif
        $('#jenis_layanan').change(function() {
            var estimasi = $('option:selected', this).data('estimasi');
            var tarif = $('option:selected', this).data('tarif');
            $('#estimasi').val(estimasi + ' hari');
            $('#tarif').val(tarif);
            updateTanggalSelesai(estimasi);
            updateTotal();
        });

        $('#berat').on('input', function() {
            updateTotal();
            updateKebutuhan();
        });

        // Auto alamat pelanggan saat antar jemput
        $('input[name="antar_jemput"]').change(function() {
            var val = $(this).val();
            if (val == "Ya") {
                var alamat = $('#id_pelanggan option:selected').data('alamat');
                if (alamat) {
                    $('#alamat_jemput').val(alamat);
                }
            } else {
                $('#alamat_jemput').val("Jln. Soekarno Hatta No. 1, Jakarta Selatan");
            }
        });

        function updateTanggalSelesai(estimasi) {
            var masuk = new Date($('#tgl_masuk').val());
            masuk.setDate(masuk.getDate() + parseInt(estimasi));
            $('#tgl_selesai').val(masuk.toISOString().split('T')[0]);
        }

        function updateTotal() {
            var berat = parseFloat($('#berat').val()) || 0;
            var tarif = parseFloat($('#tarif').val()) || 0;
            var total = berat * tarif;
            $('#total').text('Rp. ' + total.toLocaleString());
            $('#input_total').val(total);
        }

        function updateKebutuhan() {
            var berat = parseInt($('#berat').val()) || 1;
            $('#k_pewangi').text(berat);
            $('#k_pelembut').text(berat);
            $('#k_deterjen').text(berat);
        }

        // Trigger update total pada load awal
        updateTotal();
    });
</script>