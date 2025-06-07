<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil informasi foto sebelum menghapus data
    $stmt = $pdo->prepare("SELECT foto FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $candidate = $stmt->fetch();
    
    if ($candidate) {
        // Hapus file foto jika ada
        $foto_path = '../assets/images/candidates/' . $candidate['foto'];
        if (file_exists($foto_path)) {
            unlink($foto_path);
        }
        
        // Hapus data dari database
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Redirect kembali ke halaman edit candidates
header('Location: edit-candidates.php');
exit(); 