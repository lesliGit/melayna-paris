<?php
// SETUP — À exécuter UNE SEULE FOIS puis supprimer !
require 'config.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS commandes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paypal_order_id VARCHAR(50) NOT NULL,
        client_prenom VARCHAR(100),
        client_nom VARCHAR(100),
        client_email VARCHAR(200),
        client_tel VARCHAR(50),
        adresse VARCHAR(300),
        code_postal VARCHAR(10),
        ville VARCHAR(100),
        instructions TEXT,
        produits TEXT,
        sous_total DECIMAL(10,2),
        frais_livraison DECIMAL(10,2),
        total DECIMAL(10,2),
        statut VARCHAR(50) DEFAULT 'Payé',
        date_commande DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    echo "<h2 style='color:green;font-family:sans-serif;'>✅ Table commandes créée avec succès !</h2>";
    echo "<p style='font-family:sans-serif;'>Vous pouvez maintenant supprimer ce fichier setup.php du serveur.</p>";
} catch (Exception $e) {
    echo "<h2 style='color:red;font-family:sans-serif;'>❌ Erreur : " . $e->getMessage() . "</h2>";
}
?>
