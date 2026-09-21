<?php
require 'config/db.php';
$titrePage = 'Liste des livres';
$mainClass = 'page';

// On récupère ce que l'utilisateur a tapé dans la barre de recherche.
$recherche = '';
// vérifie si le paramètre q existe dans l'URL avant de le lire en gros : « est-ce que l'utilisateur a déjà tapé quelque chose dans la recherche ? »
if (isset($_GET['q'])) {
    $recherche = trim($_GET['q']);
}

// Requête pour récupérer les livres avec le nom de leur catégorie.
// Pour l'auteur, on utilise une sous-requête sur livre_auteur + auteur.
$sql = "SELECT livre.id_livre, livre.titre, livre.isbn, livre.annee_publication, livre.disponible,
               categorie.libelle AS nom_categorie
        FROM livre
        JOIN categorie ON categorie.id_categorie = livre.id_categorie
        WHERE livre.titre LIKE :recherche1
           OR categorie.libelle LIKE :recherche2
           OR livre.id_livre IN (
                SELECT livre_auteur.id_livre
                FROM livre_auteur
                JOIN auteur ON auteur.id_auteur = livre_auteur.id_auteur
                WHERE auteur.nom LIKE :recherche3
                   OR auteur.prenom LIKE :recherche4
           )
        ORDER BY livre.titre";

$requete = $pdo->prepare($sql);
$requete->execute([
    ':recherche1' => '%' . $recherche . '%',
    ':recherche2' => '%' . $recherche . '%',
    ':recherche3' => '%' . $recherche . '%',
    ':recherche4' => '%' . $recherche . '%',
]);
$livres = $requete->fetchAll();

// Pour chaque livre, on va chercher ses auteurs avec une deuxième requête.
$requeteAuteurs = $pdo->prepare(
    "SELECT auteur.nom, auteur.prenom
     FROM auteur
     JOIN livre_auteur ON livre_auteur.id_auteur = auteur.id_auteur
     WHERE livre_auteur.id_livre = :id_livre"
);

require 'includes/header.php';
?>

<h1 class="page-title">Liste des livres</h1>

<form method="get" action="livres.php" class="search-form">
    <div class="field">
        <label for="q" class="field-label">Recherche</label>
        <input type="text" id="q" name="q" class="input" placeholder="Titre, auteur ou catégorie..." value="<?php echo htmlspecialchars($recherche); ?>">
    </div>
    <button type="submit" class="btn">Rechercher</button>
    <?php if ($recherche !== ''): ?>
        <a href="livres.php" class="btn btn-outline">Réinitialiser</a>
    <?php endif; ?>
</form>

<?php if (count($livres) === 0): ?>

    <p class="lead">Aucun livre ne correspond à cette recherche.</p>

<?php else: ?>

    <div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur(s)</th>
                <th>Catégorie</th>
                <th>Année</th>
                <th>ISBN</th>
                <th>État</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livres as $livre): ?>

                <?php
                $requeteAuteurs->execute([':id_livre' => $livre['id_livre']]);
                $auteurs = $requeteAuteurs->fetchAll();

                $listeAuteurs = [];
                foreach ($auteurs as $auteur) {
                    $listeAuteurs[] = $auteur['prenom'] . ' ' . $auteur['nom'];
                }
                $texteAuteurs = implode(', ', $listeAuteurs);
                if ($texteAuteurs === '') {
                    $texteAuteurs = 'Inconnu';
                }
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                    <td><?php echo htmlspecialchars($texteAuteurs); ?></td>
                    <td><?php echo htmlspecialchars($livre['nom_categorie']); ?></td>
                    <td><?php echo htmlspecialchars($livre['annee_publication']); ?></td>
                    <td><?php echo htmlspecialchars($livre['isbn']); ?></td>
                    <td>
                        <?php if ($livre['disponible'] == 1): ?>
                            <span class="badge badge-ok">Disponible</span>
                        <?php else: ?>
                            <span class="badge badge-out">Emprunté</span>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>
