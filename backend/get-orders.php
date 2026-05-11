<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM commandes ORDER BY date_commande DESC");
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Décoder le JSON des produits pour chaque commande
    foreach ($commandes as &$c) {
        $c['produits'] = json_decode($c['produits'], true);
    }

    echo json_encode($commandes);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
