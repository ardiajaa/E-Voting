<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['candidate_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$candidate_id = $_POST['candidate_id'];

// Cek apakah user sudah memilih
$stmt = $pdo->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['has_voted']) {
    header('Location: candidates.php?error=already_voted');
    exit();
}

// Proses voting
try {
    $pdo->beginTransaction();
    
    // Insert vote
    $stmt = $pdo->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $candidate_id]);
    
    // Update status user
    $stmt = $pdo->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    $pdo->commit();
    header('Location: candidates.php?voted=1&id=' . $candidate_id);
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: candidates.php?error=failed');
}
exit(); 