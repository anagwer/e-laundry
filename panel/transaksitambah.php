<?php
if (isset($_POST['simpan'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_user = $_SESSION['user']['id_user'];
    $id_jns_layanan = $_POST['id_jns_layanan'];
    $tgl_transaksi = date('Y-m-d H:i:s');
    $tgl_mulai = date('Y-m-d');
    $tgl_selesai = $_POST['tgl_selesai'];
    $antar_jemput = $_POST['antar_jemput'];
    $alamat_jemput = $_POST['alamat_jemput'];
    $invoice = date('YmdHis');
    $berat = $_POST['berat'];
    $total = $_POST['total'];
    $metode_bayar = $_POST['metode_bayar'];

    $status_bayar = ($metode_bayar == 'Transfer') ? 'Sudah' : 'Belum';
    $status_ambil = 'Diajukan';

    // Ambil stok dan simulasikan pengurangan
    $stok_gagal = false;
    $barang_kurang = [];

    $id_jns_layanan = $_POST['id_jns_layanan'];
    $q_jenis = mysqli_query($koneksi, "SELECT jenis_layanan FROM jenis_layanan WHERE id_jns_layanan = '$id_jns_layanan'");
    $data_jenis = mysqli_fetch_assoc($q_jenis);
    $jenis_layanan_nama = $data_jenis['jenis_layanan'];

    if ($jenis_layanan_nama == 'Cuci + Setrika') {
        $barang = ['Deterjen', 'Pelembut', 'Pewangi'];
    } elseif ($jenis_layanan_nama === 'Handuk Kecil' || $jenis_layanan_nama === 'Jas' || $jenis_layanan_nama === 'Bed Cover Double') {
        $barang = ['Deterjen', 'Pelembut', 'Pewangi', 'Plastik'];
    } elseif ($jenis_layanan_nama === 'Bantal Kursi' || $jenis_layanan_nama === 'Bed Cover Single' || $jenis_layanan_nama === 'Bantal Besar') {
        $barang = ['Deterjen', 'Pelembut', 'Pewangi', 'Plastik'];
    } elseif ($jenis_layanan_nama === 'Sprei 1 Set' || $jenis_layanan_nama === 'Selimut' || $jenis_layanan_nama === 'Handuk Besar') {
        $barang = ['Deterjen', 'Pelembut', 'Pewangi', 'Plastik'];
    } elseif ($jenis_layanan_nama == 'Hanya Cuci') {
        $barang = ['Deterjen', 'Pelembut'];
    } elseif ($jenis_layanan_nama == 'Setrika') {
        $barang = ['Pewangi'];
    }
    foreach ($barang as $nama) {
        $q = mysqli_query($koneksi, "SELECT stock FROM barang WHERE nm_barang = '$nama'");
        $data = mysqli_fetch_assoc($q);
        $stok_sementara = $data['stock'] - $berat;

        if ($stok_sementara < 0) {
            $stok_gagal = true;
            $barang_kurang[] = $nama;
        }
    }

    if ($stok_gagal) {
        $list_barang = implode(', ', $barang_kurang);
        echo "<script>alert('Stok barang $list_barang tidak cukup!'); window.location='index.php?page=transaksi';</script>";
    } else {
        // Upload bukti bayar
        $bukti = '';
        if ($_FILES['bukti']['name'] != '') {
            $tmp = $_FILES['bukti']['tmp_name'];
            $name = $invoice . '-' . $_FILES['bukti']['name'];
            move_uploaded_file($tmp, '../assets/uploads/' . $name);
            $bukti = $name;
        }

        // Simpan transaksi
        $simpan = mysqli_query($koneksi, "INSERT INTO transaksi 
        (id_pelanggan, id_user, id_jns_layanan, status_bayar, status_ambil, tgl_transaksi, tgl_mulai, tgl_selesai, antar_jemput, alamat_jemput, invoice, berat, total, metode_bayar, bukti) 
        VALUES ('$id_pelanggan', '$id_user', '$id_jns_layanan', '$status_bayar', '$status_ambil', '$tgl_transaksi', '$tgl_mulai', '$tgl_selesai', '$antar_jemput', '$alamat_jemput', '$invoice', '$berat', '$total', '$metode_bayar', '$bukti')
    ");

        if ($simpan) {
            // Ambil ID transaksi yang baru saja dimasukkan
            $id_transaksi = mysqli_insert_id($koneksi);
            // Update stok
            foreach ($barang as $nama) {
                mysqli_query($koneksi, "UPDATE barang SET stock = stock - $berat WHERE nm_barang = '$nama'");
            }


            // Catat barang keluar
            foreach ($barang as $nama) {
                $qBarang = mysqli_query($koneksi, "SELECT id_barang FROM barang WHERE nm_barang = '$nama'");
                $barangData = mysqli_fetch_assoc($qBarang);
                $id_barang = $barangData['id_barang'];

                mysqli_query($koneksi, "INSERT INTO barang_keluar (id_barang, id_transaksi, tgl_keluar, jml_keluar) 
                        VALUES ('$id_barang', '$id_transaksi', '$tgl_transaksi', '$berat')");
            }

            echo "<script>alert('Transaksi berhasil ditambahkan'); window.location='index.php?page=transaksi';</script>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menambahkan transaksi.</div>";
        }
    }
}


?>

<div class="page-heading">
    <h1 class="page-title">Tambah Transaksi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item"><a href="index.php?page=transaksi">Transaksi</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="row">
        <div class="col-md-12">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Form Tambah Transaksi</div>
                </div>
                <div class="ibox-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <!-- KIRI -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pelanggan</label>
                                    <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php
                                        if ($_SESSION['user']['level_akses'] == 'Admin') {

                                            $pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan");
                                        } else {
                                            $pelanggan = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_user = '{$_SESSION['user']['id_user']}';");
                                        }
                                        while ($p = mysqli_fetch_assoc($pelanggan)) {
                                            echo "<option value='{$p['id_pelanggan']}' data-alamat='{$p['alamat']}' data-kecamatan='{$p['kecamatan']}'>{$p['nm_pelanggan']} | {$p['kecamatan']} | {$p['alamat']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Apakah antar jemput?</label><br>
                                    <label><input type="radio" name="antar_jemput" value="Ya" required> Ya</label>
                                    <label><input type="radio" name="antar_jemput" value="Tidak" required> Tidak</label>
                                </div>

                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <input type="text" name="kecamatan" id="kecamatan_jemput" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <!-- <select id="wilayah" class="form-control" disabled>
                                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                                    </select> -->
                                    <textarea name="alamat_jemput" id="alamat_jemput" class="form-control" rows="3" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Total Pembayaran</label>
                                    <h3 id="total">Rp. 0</h3>
                                    <input type="hidden" name="total" id="input_total">
                                </div>
                                <div id="warning_biaya" style="display: none; color: red;">
                                    <strong>Peringatan:</strong> Biaya antar jemput tambahan Rp. 10.000 karena di luar wilayah layanan utama.
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
                                            echo "<option value='{$k['id_kategori_layanan']}'>{$k['nm_layanan']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jenis Layanan</label>
                                    <select name="id_jns_layanan" id="jenis_layanan" class="form-control" required></select>
                                </div>

                                <div class="form-group">
                                    <label>Estimasi</label>
                                    <input type="text" id="estimasi" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Tarif</label>
                                    <input type="text" id="tarif" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label id="label_berat">Berat (kg)</label>
                                    <input type="number" name="berat" id="berat" class="form-control" required min="1" step="0.1" placeholder="Contoh: 2.9 kg">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Masuk</label>
                                    <input type="text" name="tgl_transaksi" id="tgl_masuk" class="form-control" readonly value="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="text" name="tgl_selesai" id="tgl_selesai" class="form-control" readonly>
                                </div>

                                <div class="form-group">
                                    <label>Metode Pembayaran</label>
                                    <select name="metode_bayar" class="form-control" id="metode_bayar" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="Tunai">Tunai</option>
                                    </select>
                                    <small>Contoh: 1237655794 (BCA | Muas Alingses)</small>
                                </div>

                                <div class="form-group" id="buktibayar" style="display: none;">
                                    <label>Bukti Bayar</label>
                                    <input type="file" name="bukti" class="form-control">
                                </div>

                                <?php if ($_SESSION['user']['level_akses'] == 'Admin') : ?>
                                    <div class="form-group">
                                        <h2><b>Kebutuhan Pokok (per kg)</b></h2>
                                        <div id="kebutuhan"><b>
                                            Pewangi: <span id="k_pewangi">-</span> (Tutup botol) <br>
                                            Pelembut: <span id="k_pelembut">-</span> (Sendok)<br>
                                            Deterjen: <span id="k_deterjen">-</span> (Tutup botol)<br>
                                            Plastik: <span id="k_plastik">-</span> (Lembar)
                                            </b>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
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

        $('#metode_bayar').on('change', function() {
            if ($(this).val() == 'Tunai') {
                $('#buktibayar').hide();
            } else {
                $('#buktibayar').show();
            }
        })

        // Auto isi estimasi dan tarif
        $('#jenis_layanan').change(function() {
            var estimasi = $('option:selected', this).data('estimasi');
            var tarif = $('option:selected', this).data('tarif');
            $('#estimasi').val(estimasi);
            $('#tarif').val(tarif);
            updateTanggalSelesai(estimasi);
            updateTotal();

            // Tampilkan kebutuhan bahan
            var jenisLayanan = $(this).find("option:selected").text().trim();
            var html = '';
            if (jenisLayanan === 'Cuci + Setrika') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Pelembut: <span id="k_pelembut" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="1">0</span> (Sendok)</b>';
            } else if (jenisLayanan === 'Hanya Cuci') {
                html += '<b>Pelembut: <span id="k_pelembut" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="1">0</span> (Sendok)</b>';
            } else if (jenisLayanan === 'Setrika') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="1">0</span> (Tutup botol)</b>';
            } else if (jenisLayanan === 'Handuk Kecil' || jenisLayanan === 'Jas') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Pelembut: <span id="k_pelembut" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="1">0</span> (Sendok)<br>';
                html += 'Plastik: <span id="k_plastik" data-peritem="1">0</span> (lembar)</b>';
            } else if (jenisLayanan === 'Bantal Kursi' || jenisLayanan === 'Bed Cover Single' || jenisLayanan === 'Bantal Besar') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="2">0</span> (Tutup botol)<br>';
                html += 'Pelembut: <span id="k_pelembut" data-peritem="1">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="3">0</span> (Sendok)<br>';
                html += 'Plastik: <span id="k_plastik" data-peritem="1">0</span> (lembar)</b>';
            } else if (jenisLayanan === 'Bed Cover Double') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="4">0</span> (Tutup botol)<br>';
                html += 'Pelembut: <span id="k_pelembut" data-peritem="2">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="4">0</span> (Sendok)<br>';
                html += 'Plastik: <span id="k_plastik" data-peritem="1">0</span> (lembar)</b>';
            } else if (jenisLayanan === 'Sprei 1 Set' || jenisLayanan === 'Selimut' || jenisLayanan === 'Handuk Besar') {
                html += '<b>Pewangi: <span id="k_pewangi" data-peritem="2">0</span> (Tutup botol)<br>';
                html += 'Pelembut: <span id="k_pelembut" data-peritem="2">0</span> (Tutup botol)<br>';
                html += 'Deterjen: <span id="k_deterjen" data-peritem="2">0</span> (Sendok)<br>';
                html += 'Plastik: <span id="k_plastik" data-peritem="1">0</span> (lembar)</b>';
            }

            $('#kebutuhan').html(html);

            updateKebutuhan(); // panggil sekali untuk inisialisasi
        });

        $('#berat').on('input', function() {
            updateTotal();
            updateKebutuhan();
        });

        // Auto alamat pelanggan saat antar jemput
        $('input[name="antar_jemput"]').change(function() {
            updateTotal();
            var val = $(this).val();
            if (val == "Ya") {
                var alamat = $('#id_pelanggan option:selected').data('alamat');
                var kecamatan = $('#id_pelanggan option:selected').data('kecamatan');
                $('#alamat_jemput').val(alamat);
                $('#kecamatan_jemput').val(kecamatan);
            } else {
                $('#alamat_jemput').val("Jl. Fatmawati Soekarno, Taba Lestari, Kec. Lubuklinggau Timur I, Kota. Lubuklinggau, Sumatera Selatan");
                $('#kecamatan_jemput').val("LUBUK LINGGAU SELATAN II");
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

            // Cek apakah antar jemput = Ya
            var antarJemput = $('input[name="antar_jemput"]:checked').val();
            if (antarJemput === 'Ya') {
                var kecamatan = $('#id_pelanggan option:selected').data('kecamatan');
                // Jika kecamatan bukan Lubuk Linggau Selatan 2 atau Lubuk Linggau Timur 1
                if (kecamatan !== 'LUBUK LINGGAU SELATAN II' && kecamatan !== 'LUBUK LINGGAU TIMUR I') {
                    total += 10000; // Tambahkan biaya tambahan 10.000
                    $('#warning_biaya').show(); // Tampilkan warning
                } else {
                    $('#warning_biaya').hide(); // Sembunyikan warning
                }
            } else {
                $('#warning_biaya').hide(); // Sembunyikan warning jika tidak antar jemput
            }

            $('#total').text('Rp. ' + total.toLocaleString());
            $('#input_total').val(total);
        }

        function updateKebutuhan() {
            var jumlah = Math.ceil(parseFloat($('#berat').val())) || 1;
            if ($('#k_pewangi').length) {
                var perItem = parseInt($('#k_pewangi').data('peritem')) || 1;
                $('#k_pewangi').text(jumlah * perItem);
            }
            if ($('#k_pelembut').length) {
                var perItem = parseInt($('#k_pelembut').data('peritem')) || 1;
                $('#k_pelembut').text(jumlah * perItem);
            }
            if ($('#k_deterjen').length) {
                var perItem = parseInt($('#k_deterjen').data('peritem')) || 1;
                $('#k_deterjen').text(jumlah * perItem);
            }
            if ($('#k_plastik').length) {
                var perItem = parseInt($('#k_plastik').data('peritem')) || 1;
                $('#k_plastik').text(jumlah * perItem);
            }
        }


        // Fungsi untuk update label Berat menjadi Jumlah Cucian jika jenis layanan = Laundry Satuan
        function updateLabelBerat() {
            var kategoriLayanan = $('#kategori option:selected').text();
            if (kategoriLayanan === 'Laundry Satuan') {
                $('#label_berat').text('Jumlah Cucian');
                $('#berat').attr('placeholder', 'Contoh: 5');
                $('#berat').attr('min', '1');
            } else {
                $('#label_berat').text('Berat (kg)');
                $('#berat').attr('placeholder', 'Dalam kilogram');
            }
        }

        // Panggil fungsi saat kategori berubah
        $('#kategori').change(function () {
            updateLabelBerat();
        });
    });
</script>