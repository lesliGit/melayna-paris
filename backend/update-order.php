<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

try {
    $fields = [];
    $values = [];

    if (isset($data['statut'])) {
        $fields[] = "statut = ?";
        $values[] = $data['statut'];
    }
    if (isset($data['tracking'])) {
        $fields[] = "tracking = ?";
        $values[] = $data['tracking'];
    }

    if (empty($fields)) {
        echo json_encode(['success' => false, 'error' => 'Rien à mettre à jour']);
        exit;
    }

    $values[] = $data['id'];
    $stmt = $pdo->prepare("UPDATE commandes SET " . implode(', ', $fields) . " WHERE id = ?");
    $stmt->execute($values);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
