<?php

$host = 'localhost';
$dbname = 'transportasi2';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// CRUD untuk Kereta
function getKereta($pdo) {
    $query = "SELECT kereta.no_ka, kereta.nama_kereta, jadwal_kereta.waktu_berangkat, jadwal_kereta.waktu_tiba
              FROM kereta 
              INNER JOIN jadwal_kereta ON kereta.no_ka = jadwal_kereta.no_ka";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addKereta($pdo, $no_ka, $nama_kereta) {
    $stmt = $pdo->prepare("INSERT INTO kereta (no_ka, nama_kereta) VALUES (?, ?)");
    return $stmt->execute([$no_ka, $nama_kereta]);
}

function updateKereta($pdo, $no_ka, $nama_kereta) {
    $stmt = $pdo->prepare("UPDATE kereta SET nama_kereta=? WHERE no_ka=?");
    return $stmt->execute([$nama_kereta, $no_ka]);
}

function deleteKereta($pdo, $no_ka) {
    $stmt = $pdo->prepare("DELETE FROM kereta WHERE no_ka=?");
    return $stmt->execute([$no_ka]);
}

// CRUD untuk Penumpang
function getPenumpang($pdo) {
    $stmt = $pdo->query("SELECT * FROM penumpang");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addPenumpang($pdo, $nama_penumpang, $tipe_penumpang) {
    $stmt = $pdo->prepare("INSERT INTO penumpang (nama_penumpang, tipe_penumpang) VALUES (?, ?)");
    return $stmt->execute([$nama_penumpang, $tipe_penumpang]);
}

function updatePenumpang($pdo, $id_penumpang, $nama_penumpang, $tipe_penumpang) {
    $stmt = $pdo->prepare("UPDATE penumpang SET nama_penumpang=?, tipe_penumpang=? WHERE id_penumpang=?");
    return $stmt->execute([$nama_penumpang, $tipe_penumpang, $id_penumpang]);
}

function deletePenumpang($pdo, $id_penumpang) {
    $stmt = $pdo->prepare("DELETE FROM penumpang WHERE id_penumpang=?");
    return $stmt->execute([$id_penumpang]);
}

// CRUD untuk Stasiun
function getStasiun($pdo) {
    $stmt = $pdo->query("SELECT * FROM stasiun");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addStasiun($pdo, $kode_stasiun, $nama_stasiun) {
    $stmt = $pdo->prepare("INSERT INTO stasiun (kode_stasiun, nama_stasiun) VALUES (?, ?)");
    return $stmt->execute([$kode_stasiun, $nama_stasiun]);
}

function updateStasiun($pdo, $kode_stasiun, $nama_stasiun) {
    $stmt = $pdo->prepare("UPDATE stasiun SET nama_stasiun=? WHERE kode_stasiun=?");
    return $stmt->execute([$nama_stasiun, $kode_stasiun]);
}

function deleteStasiun($pdo, $kode_stasiun) {
    $stmt = $pdo->prepare("DELETE FROM stasiun WHERE kode_stasiun=?");
    return $stmt->execute([$kode_stasiun]);
}

// CRUD untuk Rute Kereta
function getRute($pdo) {
    $stmt = $pdo->query("SELECT * FROM rute_kereta");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addRute($pdo, $no_ka, $kode_stasiun_asal, $kode_stasiun_tujuan) {
    $stmt = $pdo->prepare("INSERT INTO rute_kereta (no_ka, kode_stasiun_asal, kode_stasiun_tujuan) VALUES (?, ?, ?)");
    return $stmt->execute([$no_ka, $kode_stasiun_asal, $kode_stasiun_tujuan]);
}

function deleteRute($pdo, $no_ka) {
    $stmt = $pdo->prepare("DELETE FROM rute_kereta WHERE no_ka=?");
    return $stmt->execute([$no_ka]);
}

// CRUD untuk Tiket
function getTiket($pdo) {
    $stmt = $pdo->query("SELECT * FROM tiket");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addTiket($pdo, $id_penumpang, $no_ka, $waktu_berangkat, $waktu_tiba, $no_kursi, $gerbong) {
    $stmt = $pdo->prepare("INSERT INTO tiket (id_penumpang, no_ka, waktu_berangkat, waktu_tiba, no_kursi, gerbong) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$id_penumpang, $no_ka, $waktu_berangkat, $waktu_tiba, $no_kursi, $gerbong]);
}

function deleteTiket($pdo, $kode_booking) {
    $stmt = $pdo->prepare("DELETE FROM tiket WHERE kode_booking=?");
    return $stmt->execute([$kode_booking]);
}
?>
