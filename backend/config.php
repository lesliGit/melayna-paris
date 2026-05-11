<?php
// Configuration base de données Melayna Paris
define('DB_HOST', 'sql308.infinityfree.com');
define('DB_USER', 'if0_41889700');
define('DB_PASS', 'Taylena123');
define('DB_NAME', 'if0_41889700_commandes');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connexion base de données échouée']);
    exit;
}
?>
