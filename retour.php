<?php
require 'config/db.php';

// Cette page ne s'utilise que par le bouton "Retour" du formulaire (méthode POST).
// Si on l'ouvre directement dans le navigateur, on renvoie vers la liste des emprunts.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: emprunts.php');
    exit;
}

$idEmprunt = $_POST['id_emprunt'];

// On récupère l'emprunt pour connaître le livre concerné,
// et on vérifie qu'il n'est pas déjà rendu (date_retour encore vide).
$recherche = $pdo->prepare(
    "SELECT id_livre FROM emprunt WHERE id_emprunt = :id_emprunt AND date_retour IS NULL"
);
$recherche->execute([':id_emprunt' => $idEmprunt]);
$emprunt = $recherche->fetch();

if ($emprunt) {

    // 1. On enregistre la date de retour (aujourd'hui).
    $majEmprunt = $pdo->prepare("UPDATE emprunt SET date_retour = CURDATE() WHERE id_emprunt = :id_emprunt");
    $majEmprunt->execute([':id_emprunt' => $idEmprunt]);

    // 2. Le livre redevient disponible.
    $majLivre = $pdo->prepare("UPDATE livre SET disponible = 1 WHERE id_livre = :id_livre");
    $majLivre->execute([':id_livre' => $emprunt['id_livre']]);
}

// On revient à la liste des emprunts, avec un message de confirmation dans l'adresse.
header('Location: emprunts.php?retour=1');
exit;
