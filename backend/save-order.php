<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'config.php';
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['paypal_order_id'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

// Fonction envoi email via Gmail SMTP
function sendEmail($to, $toName, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'Taylena.shop@gmail.com';
        $mail->Password   = 'vsnybnfpyubnzpjw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('Taylena.shop@gmail.com', 'Melayna Paris');
        $mail->addAddress($to, $toName);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->isHTML(false);
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

try {
    // Enregistrer la commande en base de données
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
        $data['sous_total']      ?? 0,
        $data['frais_livraison'] ?? 0,
        $data['total']           ?? 0
    ]);

    // Construire la liste des produits
    $produitsList = '';
    foreach (($data['produits'] ?? []) as $p) {
        $produitsList .= "- " . $p['name'] . " (x" . $p['qty'] . ") - " . number_format($p['price'] * $p['qty'], 2, ',', ' ') . " €\n";
    }

    // Email de confirmation au client
    if (!empty($data['email'])) {
        $messageClient = "Bonjour " . $data['prenom'] . ",\n\n"
            . "Merci pour votre commande sur Melayna Paris !\n\n"
            . "📦 RÉCAPITULATIF\n"
            . "---------------\n"
            . $produitsList
            . "\nTotal : " . number_format($data['total'], 2, ',', ' ') . " €\n\n"
            . "📍 ADRESSE DE LIVRAISON\n"
            . $data['adresse'] . "\n"
            . $data['code_postal'] . " " . $data['ville'] . "\n\n"
            . "Nous vous contacterons dès l'expédition de votre commande.\n\n"
            . "À très bientôt,\n"
            . "L'équipe Melayna Paris 💗";

        sendEmail(
            $data['email'],
            $data['prenom'] . ' ' . $data['nom'],
            "✅ Confirmation de votre commande – Melayna Paris",
            $messageClient
        );
    }

    // Email de notification à la boutique
    $messageVendeur = "🛍️ Nouvelle commande reçue !\n\n"
        . "Client : " . $data['prenom'] . " " . $data['nom'] . "\n"
        . "Email : " . $data['email'] . "\n"
        . "Téléphone : " . $data['tel'] . "\n"
        . "Adresse : " . $data['adresse'] . ", " . $data['code_postal'] . " " . $data['ville'] . "\n\n"
        . "Produits :\n" . $produitsList
        . "\nTotal : " . number_format($data['total'], 2, ',', ' ') . " €\n"
        . "PayPal ID : " . $data['paypal_order_id'];

    sendEmail(
        'Taylena.shop@gmail.com',
        'Melayna Paris',
        '🛍️ Nouvelle commande Melayna Paris',
        $messageVendeur
    );

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
