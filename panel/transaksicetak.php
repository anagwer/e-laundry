<?php
include '../koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "
    SELECT t.*, 
           p.nm_pelanggan, p.no_telp, p.alamat, 
           u.nm_user, 
           k.nm_layanan, 
           j.jenis_layanan, j.tarif
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN user u ON t.id_user = u.id_user
    LEFT JOIN jenis_layanan j ON t.id_jenis_layanan = j.id_jns_layanan
    LEFT JOIN kategori_layanan k ON j.id_kategori_layanan = k.id_kategori_layanan
    WHERE t.id_transaksi = '$id'
");
$trans = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cetak Transaksi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            font-size: 14px;
            color: #333;
        }

        .box {
            border: 1px solid #ccc;
            padding: 25px 30px;
            border-radius: 5px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #2b6cb0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 8px 10px;
            border: 1px solid #bbb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }

        .bold {
            font-weight: bold;
        }

        .section-title {
            margin-top: 25px;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .text-right {
            text-align: right;
        }

        .info-table td {
            border: none;
            padding: 3px 0;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="box">
        <div class="header">
            <div class="title">
                Transaksi: <span style="color: #3182ce;">INV<?= $trans['id_transaksi'] . '-' . date('dmY', strtotime($trans['tgl_transaksi'])) ?></span>
            </div>
            <div class="text-right">
                <div><strong>Invoice #<?= $trans['invoice'] ?></strong></div>
                <div>Tgl Transaksi: <?= date('d-m-Y', strtotime($trans['tgl_transaksi'])) ?></div>
            </div>
        </div>

        <div class="section-title">Informasi</div>
        <table class="info-table">
            <tr>
                <td class="bold">Nama Pelanggan</td>
                <td>: <?= $trans['nm_pelanggan'] ?></td>
                <td class="bold">Petugas Entri</td>
                <td>: <?= $trans['nm_user'] ?></td>
            </tr>
            <tr>
                <td class="bold">Telepon</td>
                <td>: <?= $trans['no_telp'] ?></td>
                <td class="bold">Tanggal Masuk</td>
                <td>: <?= date('d-m-Y', strtotime($trans['tgl_mulai'])) ?></td>
            </tr>
            <tr>
                <td class="bold">Alamat</td>
                <td>: <?= $trans['alamat'] ?></td>
                <td class="bold">Tanggal Selesai</td>
                <td>: <?= date('d-m-Y', strtotime($trans['tgl_selesai'])) ?></td>
            </tr>
            <tr>
                <td class="bold">Metode Bayar</td>
                <td>: <?= $trans['metode_bayar'] ?></td>
                <td class="bold">Tanggal Diambil</td>
                <td>: <?= $trans['tgl_diambil'] ? date('d-m-Y', strtotime($trans['tgl_diambil'])) : '-' ?></td>
            </tr>
        </table>

        <div class="section-title">Detail Layanan</div>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Tarif</th>
                    <th>Berat</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $trans['nm_layanan'] ?></td>
                    <td><?= $trans['jenis_layanan'] ?></td>
                    <td>Rp <?= number_format($trans['tarif'], 0, ',', '.') ?></td>
                    <td><?= $trans['berat'] ?> Kg</td>
                    <td>Rp <?= number_format($trans['total'], 0, ',', '.') ?></td>
                    <td>
                        <?= $trans['status_bayar'] == 'Sudah' ? '<b>Sudah dibayar</b>' : '<b>Belum dibayar</b>' ?><br>
                        <?= $trans['status_ambil'] == 'Selesai Sudah Diambil' ? '<b>Sudah diambil</b>' : '<b>Belum diambil</b>' ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="text-right" style="margin-top: 30px;">
            Dicetak pada: <?= date('d-m-Y') ?>
        </p>
    </div>

</body>

</html>