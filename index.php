<?php
require 'config/db.php';
$titrePage = 'Accueil';
$afficherChargement = true; // pour le chargement
$mainClass = 'home';

// Nombre total de livres.
$nombreLivres = $pdo->query("SELECT COUNT(*) FROM livre")->fetchColumn();

// Nombre total d'adhérents.
$nombreAdherents = $pdo->query("SELECT COUNT(*) FROM adherent")->fetchColumn();

// Nombre d'emprunts en cours (pas encore rendus).
$nombreEmpruntsEnCours = $pdo->query(
    "SELECT COUNT(*) FROM emprunt WHERE date_retour IS NULL"
)->fetchColumn();

// Nombre d'emprunts en retard : pas encore rendus ET date de retour prévue déjà passée.
$nombreRetards = $pdo->query(
    "SELECT COUNT(*) FROM emprunt WHERE date_retour IS NULL AND date_retour_prevue < CURDATE()"
)->fetchColumn();

require 'includes/header.php';
?>

<div class="hero">
    <div class="hero-text">
        <h1 class="page-title">Bienvenue à la médiathèque</h1>
        <p class="lead">Cette application permet de consulter les livres et les adhérents de la médiathèque, et d'enregistrer les emprunts et les retours. Utilisez le menu en haut de la page pour naviguer.</p>
    </div>
    <div class="spines">
        <div class="spine spine-1"></div>
        <div class="spine spine-2"></div>
        <div class="spine spine-3"></div>
        <div class="spine spine-4"></div>
        <div class="spine spine-5"></div>
        <div class="spine spine-6"></div>
    </div>
</div>

<div class="stats">
    <h2 class="stats-title">En un coup d'œil</h2>
    <div class="stats-grid">
        <div class="stat">
            <div class="stat-circle">
                <span class="stat-number"><?php echo $nombreLivres; ?></span>
            </div>
            <span class="stat-label">Livres</span>
        </div>
        <div class="stat">
            <div class="stat-circle">
                <span class="stat-number"><?php echo $nombreAdherents; ?></span>
            </div>
            <span class="stat-label">Adhérents</span>
        </div>
        <div class="stat">
            <div class="stat-circle">
                <span class="stat-number"><?php echo $nombreEmpruntsEnCours; ?></span>
            </div>
            <span class="stat-label">Emprunts en cours</span>
        </div>
        <div class="stat">
            <div class="stat-circle stat-circle-late">
                <span class="stat-number stat-number-late"><?php echo $nombreRetards; ?></span>
            </div>
            <span class="stat-label">En retard</span>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
