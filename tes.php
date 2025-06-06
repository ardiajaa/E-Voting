<?php
$password = 'mahameru';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hashed Password: " . $hashedPassword;
?>


