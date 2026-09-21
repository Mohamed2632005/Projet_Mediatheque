<?php

// les config pour identifier notre DB
$host = '127.0.0.1';
$nomBase = 'mediatheque';
$utilisateur = 'root';
$motDePasse = '';

try {
    // On crée la connexion. $pdo va servir à envoyer des requêtes SQL depuis PHP.
    $pdo = new PDO("mysql:host=$host;dbname=$nomBase;charset=utf8mb4", $utilisateur, $motDePasse);

    // Cette ligne dit à PDO : si une requête SQL est fausse, affiche une erreur claire
    // au lieu d'échouer sans rien dire.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $erreur) {
    // Si la connexion échoue (MySQL éteint, mauvais nom de base...), on arrête la page
    // avec un message simple, sans afficher les détails techniques.
    die('Erreur de connexion à la base de données.');
}
