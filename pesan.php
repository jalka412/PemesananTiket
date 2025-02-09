<?php
require 'database.php';

if (!isset($_GET['no_ka'])) {
    die("Nomor KA tidak ditemukan.");
}

$keretaList = getKereta($pdo);
$no_ka = $_GET['no_ka'];

$stmt = $pdo->prepare("SELECT waktu_berangkat, waktu_tiba FROM jadwal_kereta WHERE no_ka = ?");
$stmt->execute([$no_ka]);
$jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jadwal) {
    die("Jadwal tidak ditemukan untuk KA ini.");
}

$waktu_berangkat = $jadwal['waktu_berangkat'];
$waktu_tiba = $jadwal['waktu_tiba'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_penumpang = generateRandomID($pdo);
    $nama_penumpang = $_POST['nama_penumpang'];
    $tipe_penumpang = $_POST['tipe_penumpang'];
    $no_kursi = $_POST['no_kursi'];
    $gerbong = $_POST['gerbong'];

    $stmt = $pdo->prepare("INSERT INTO penumpang (id_penumpang, nama_penumpang, tipe_penumpang) VALUES (?, ?, ?)");
    $stmt->execute([$id_penumpang, $nama_penumpang, $tipe_penumpang]);

    $stmt = $pdo->prepare("INSERT INTO tiket (id_penumpang, no_ka, waktu_berangkat, waktu_tiba, no_kursi, gerbong) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_penumpang, $no_ka, $waktu_berangkat, $waktu_tiba, $no_kursi, $gerbong]);

    
    echo "<script>alert('Tiket berhasil dipesan! ID Penumpang Anda: $id_penumpang'); window.location='index.php';</script>";
}

    // Fungsi untuk membuat ID Penumpang Acak (5 Digit)
    function generateRandomID($pdo) {
        do {
            $randomID = rand(10000, 99999);
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM penumpang WHERE id_penumpang = ?");
            $stmt->execute([$randomID]);
            $count = $stmt->fetchColumn();
        } while ($count > 0);
        
        return $randomID;
        }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            width: 800px;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }
        .seat {
            width: 40px;
            height: 40px;
            margin: 5px;
            text-align: center;
            line-height: 40px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }
        .available { background: white; color: black; }
        .available:hover { background: #ffc107; }
        .selected { background: #28a745; color: white; }
        .occupied { background: #dc3545; color: white; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Pesan Tiket KA <i class="fas fa-train"></i></h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Penumpang:</label>
                <input type="text" name="nama_penumpang" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tipe Penumpang:</label>
                <select name="tipe_penumpang" class="form-control" required>
                    <option value="umum">Umum</option>
                    <option value="bayi">Bayi</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Pilih Gerbong:</label>
                <select name="gerbong" id="gerbong" class="form-control">
                    <option value="">Pilih Gerbong</option>
                    <option value="pre1">PRE-1</option>
                    <option value="pre2">PRE-2</option>
                    <option value="eks1">EKS-1</option>
                    <option value="eks2">EKS-2</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Nomor Kursi:</label>
                <input type="text" name="no_kursi" id="kursiInput" class="form-control" required readonly>
            </div>
            <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#pilihKursi">
                <i class="fas fa-chair"></i> Pilih Kursi
            </button>
            <button type="submit" class="btn btn-success w-100 mt-3">
                <i class="fas fa-ticket-alt"></i> Pesan Tiket
            </button>
        </form>
    </div>

    <div class="modal fade" id="pilihKursi" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Kursi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap justify-content-center" id="seatMap"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmSeat">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const seatMap = document.getElementById("seatMap");
        const kursiInput = document.getElementById("kursiInput");
        const confirmSeatButton = document.getElementById("confirmSeat");
        const modal = new bootstrap.Modal(document.getElementById("pilihKursi"));
        const rows = ["A", "B", "C", "D"];
        const cols = 10;
        let selectedSeat = null;

        function generateSeats() {
            seatMap.innerHTML = "";
            rows.forEach(row => {
                for (let i = 1; i <= cols; i++) {
                    const seat = document.createElement("div");
                    seat.classList.add("seat", "available");
                    seat.textContent = row + i;
                    seat.addEventListener("click", function() {
                        document.querySelectorAll(".seat.selected").forEach(s => s.classList.remove("selected"));
                        seat.classList.add("selected");
                        selectedSeat = seat.textContent;
                    });
                    seatMap.appendChild(seat);
                }
                seatMap.appendChild(document.createElement("br"));
            });
        }

        confirmSeatButton.addEventListener("click", function() {
            if (selectedSeat) {
                kursiInput.value = selectedSeat;
                modal.hide();
            } else {
                alert("Silakan pilih kursi terlebih dahulu.");
            }
        });

        generateSeats();
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

