<?php
// Ajouter colonne tracking — exécuter UNE SEULE FOIS puis supprimer
require 'config.php';
try {
    $pdo->exec("ALTER TABLE commandes ADD COLUMN IF NOT EXISTS tracking VARCHAR(100) DEFAULT ''");
    echo "<h2 style='color:green;font-family:sans-serif;'>✅ Colonne tracking ajoutée !</h2>";
    echo "<p style='font-family:sans-serif;'>Supprimez ce fichier migrate.php du serveur.</p>";
} catch (Exception $e) {
    echo "<h2 style='color:red;font-family:sans-serif;'>❌ " . $e->getMessage() . "</h2>";
}
?>
