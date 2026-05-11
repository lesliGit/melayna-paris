<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['paypal_order_id'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO commandes
        (paypal_order_id, client_prenom, client_nom, client_email, client_tel,
         adresse, code_postal, ville, instructions, produits, sous_total, frais_livraison, total)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['paypal_order_id'],
        $data['prenom']       ?? '',
        $data['nom']          ?? '',
        $data['email']        ?? '',
        $data['tel']          ?? '',
        $data['adresse']      ?? '',
        $data['code_postal']  ?? '',
        $data['ville']        ?? '',
        $data['instructions'] ?? '',
        json_encode($data['produits'] ?? []),
        $data['sous_total']       ?? 0,
        $data['frais_livraison']  ?? 0,
        $data['total']            ?? 0
    ]);

    // Email de confirmation au client
    if (!empty($data['email'])) {
        $produitsList = '';
        foreach (($data['produits'] ?? []) as $p) {
            $produitsList .= "- " . $p['name'] . " (x" . $p['qty'] . ") - " . number_format($p['price'] * $p['qty'], 2, ',', ' ') . " €\n";
        }

        $sujet = "✅ Confirmation de votre commande – Melayna Paris";
        $message = "Bonjour " . $data['prenom'] . ",\n\n"
            . "Merci pour votre commande sur Melayna Paris !\n\n"
            . "📦 RÉCAPITULATIF\n"
            . "---------------\n"
            . $produitsList
            . "\nTotal : " . number_format($data['total'], 2, ',', ' ') . " €\n\n"
            . "📍 LIVRAISON\n"
            . $data['adresse'] . "\n"
            . $data['code_postal'] . " " . $data['ville'] . "\n\n"
            . "Nous vous contacterons dès l'expédition de votre commande.\n\n"
            . "À très bientôt,\n"
            . "L'équipe Melayna Paris";

        $headers = "From: commandes@melayna-paris.infinityfreeapp.com\r\n"
                 . "Reply-To: contact@melayna-paris.com\r\n"
                 . "Content-Type: text/plain; charset=UTF-8";

        mail($data['email'], $sujet, $message, $headers);
    }

    // Email de notification à la boutique
    $notifMessage = "Nouvelle commande reçue !\n\n"
        . "Client : " . $data['prenom'] . " " . $data['nom'] . "\n"
        . "Email : " . $data['email'] . "\n"
        . "Téléphone : " . $data['tel'] . "\n"
        . "Adresse : " . $data['adresse'] . ", " . $data['code_postal'] . " " . $data['ville'] . "\n"
        . "Total : " . number_format($data['total'], 2, ',', ' ') . " €\n"
        . "PayPal ID : " . $data['paypal_order_id'];

    mail('lesli.delabursi@dragonbleu.fr', '🛍️ Nouvelle commande Melayna Paris', $notifMessage,
        "From: commandes@melayna-paris.infinityfreeapp.com\r\nContent-Type: text/plain; charset=UTF-8");

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
