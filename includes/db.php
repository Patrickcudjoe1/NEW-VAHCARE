<?php
// Database connection configuration

$host = 'localhost';
$db   = 'vah_care';
$user = 'root';
$pass = ''; // Default for many local environments
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, log error and show generic message
    die("Connection failed: " . $e->getMessage());
}
?>
