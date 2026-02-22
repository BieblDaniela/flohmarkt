<?php
// db.php
$config = require 'config.php';



$dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Wirft Fehler bei Problemen
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Gibt Arrays zurück
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Erhöht Sicherheit gegen SQL-Injection
];

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>