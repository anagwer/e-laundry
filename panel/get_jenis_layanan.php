<?php
include '../koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id_kategori = $_GET['id'];

    $query = mysqli_query($koneksi, "SELECT * FROM jenis_layanan WHERE id_kategori_layanan = '$id_kategori'");
    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        // Tentukan bahan pokok berdasarkan jenis layanan
        $bahan = [
            'pewangi' => false,
            'pelembut' => false,
            'deterjen' => false
        ];

        if ($row['jenis_layanan'] == 'Cuci + Setrika') {
            $bahan['pewangi'] = true;
            $bahan['pelembut'] = true;
            $bahan['deterjen'] = true;
        } elseif ($row['jenis_layanan'] == 'Hanya Cuci') {
            $bahan['pelembut'] = true;
            $bahan['deterjen'] = true;
        } elseif ($row['jenis_layanan'] == 'Setrika') {
            $bahan['pewangi'] = true;
        }

        $data[] = [
            'id_jns_layanan' => $row['id_jns_layanan'],
            'jenis_layanan' => $row['jenis_layanan'],
            'estimasi_waktu' => $row['estimasi_waktu'],
            'tarif' => $row['tarif'],
            'bahan' => $bahan
        ];
    }

    echo json_encode($data);
} else {
    echo json_encode([]);
}