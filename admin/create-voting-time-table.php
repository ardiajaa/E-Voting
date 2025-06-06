<?php
require_once '../config/database.php';

try {
    // Buat tabel voting_time
    $pdo->exec("CREATE TABLE IF NOT EXISTS voting_time (
        id INT AUTO_INCREMENT PRIMARY KEY,
        start_time DATETIME NOT NULL,
        end_time DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "Tabel voting_time berhasil dibuat!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 