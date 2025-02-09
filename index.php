<?php
require 'database.php';

// Ambil daftar kereta dari database
$keretaList = getKereta($pdo);



$search = $_GET['search'] ?? '';
$query = "SELECT * FROM kereta WHERE no_ka LIKE ? OR nama_kereta LIKE ?";
$stmt = $pdo->prepare($query);
$stmt->execute(["%$search%", "%$search%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Tiket Kereta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 20px; }
        .table th { background-color: #007bff; color: white; }
        .btn-pesan { background-color: #28a745; color: white; }
        .btn-pesan:hover { background-color: #218838; }
        .search-bar { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center my-4">Pencarian Tiket Kereta</h2>
        <form method="GET" class="search-bar d-flex">
            <input type="text" class="form-control me-2" name="search" placeholder="Cari berdasarkan No KA atau Nama Kereta" value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <?php if ($search): ?>
            <h4>Hasil Pencarian:</h4>
            <table class="table table-striped">
                <tr>
                    <th>No KA</th>
                    <th>Nama Kereta</th>
                    <th>Aksi</th>
                </tr>
                
                <?php if (count($results) > 0): ?>
                    <?php foreach ($results as $kereta): ?>
                        <tr>
                            <td><?= $kereta['no_ka']; ?></td>
                            <td><?= $kereta['nama_kereta']; ?></td>
                            <td><a href="pesan.php?no_ka=<?= $kereta['no_ka']; ?>" class="btn btn-sm btn-pesan">Pesan Tiket</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada hasil ditemukan</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>

        <h2 class="text-center my-4">Daftar Kereta</h2>
        <table class="table table-hover">
            <tr>
                <th>No KA</th>
                <th>Nama Kereta</th>
                
                <th>Waktu Berangkat</th>
                <th>waktu Tiba</th>
            </tr>
            <?php foreach ($keretaList as $kereta): ?>
            <tr>
                <td><?= $kereta['no_ka']; ?></td>
                <td><?= $kereta['nama_kereta']; ?></td>
                <td><?= $kereta['waktu_berangkat']; ?></td>
                <td><?= $kereta['waktu_tiba']; ?></td>
                
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>