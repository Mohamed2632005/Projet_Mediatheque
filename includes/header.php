<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titrePage); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php if (isset($afficherChargement)): ?>
    <div class="ecran-chargement">
        <span class="chargement-nom">Médiathèque</span>
        <span class="chargement-barre"></span>
    </div>
<?php endif; ?>

<?php $pageActuelle = basename($_SERVER['PHP_SELF']); ?>
<header class="site-header">
    <a href="index.php" class="site-logo">Médiathèque</a>
    <nav class="site-nav">
        <a href="index.php" class="nav-link<?php if ($pageActuelle === 'index.php') echo ' nav-link-active'; ?>">Accueil</a>
        <a href="livres.php" class="nav-link<?php if ($pageActuelle === 'livres.php') echo ' nav-link-active'; ?>">Livres</a>
        <a href="adherents.php" class="nav-link<?php if ($pageActuelle === 'adherents.php') echo ' nav-link-active'; ?>">Adhérents</a>
        <a href="emprunts.php" class="nav-link<?php if ($pageActuelle === 'emprunts.php') echo ' nav-link-active'; ?>">Emprunts en cours</a>
        <a href="emprunter.php" class="nav-link<?php if ($pageActuelle === 'emprunter.php') echo ' nav-link-active'; ?>">Nouvel emprunt</a>
    </nav>
</header>

<main class="<?php echo htmlspecialchars($mainClass ?? ''); ?>">
