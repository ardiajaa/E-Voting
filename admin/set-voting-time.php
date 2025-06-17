<?php
require_once '../config/database.php';
session_start();

// Set zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Cek apakah user adalah admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Validasi input
if (!isset($_POST['start_time']) || !isset($_POST['end_time'])) {
    echo json_encode(['success' => false, 'message' => 'Waktu mulai dan selesai harus diisi']);
    exit;
}

$start_time = date('Y-m-d H:i:s', strtotime($_POST['start_time']));
$end_time = date('Y-m-d H:i:s', strtotime($_POST['end_time']));

// Validasi format waktu
if (!strtotime($start_time) || !strtotime($end_time)) {
    echo json_encode(['success' => false, 'message' => 'Format waktu tidak valid']);
    exit;
}

// Validasi waktu
if (strtotime($start_time) >= strtotime($end_time)) {
    echo json_encode(['success' => false, 'message' => 'Waktu mulai harus lebih awal dari waktu selesai']);
    exit;
}

try {
    // Cek apakah sudah ada data waktu voting
    $stmt = $pdo->query("SELECT COUNT(*) FROM voting_time");
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Update waktu yang ada
        $stmt = $pdo->prepare("UPDATE voting_time SET start_time = ?, end_time = ? ORDER BY id DESC LIMIT 1");
    } else {
        // Insert waktu baru
        $stmt = $pdo->prepare("INSERT INTO voting_time (start_time, end_time) VALUES (?, ?)");
    }

    $stmt->execute([$start_time, $end_time]);
    
    echo json_encode(['success' => true, 'message' => 'Waktu voting berhasil diatur']);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan waktu voting']);
} 