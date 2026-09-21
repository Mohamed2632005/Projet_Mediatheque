<?php
require 'config/db.php';
$titrePage = 'Emprunts en cours';
$mainClass = 'page';

// Un emprunt "en cours" est un emprunt dont la date de retour n'a pas encore été remplie.
$sql = "SELECT emprunt.id_emprunt, adherent.nom, adherent.prenom, livre.titre,
               emprunt.date_emprunt, emprunt.date_retour_prevue
        FROM emprunt
        JOIN adherent ON adherent.id_adherent = emprunt.id_adherent
        JOIN livre ON livre.id_livre = emprunt.id_livre
        WHERE emprunt.date_retour IS NULL
        ORDER BY emprunt.date_retour_prevue";

// Exécute la requête et récupère tous les emprunts en cours dans un tableau.
$emprunts = $pdo->query($sql)->fetchAll();

// La date d'aujourd'hui, au format utilisé par MySQL (AAAA-MM-JJ),
// pour comparer avec la date de retour prévue de chaque emprunt.
$aujourdhui = date('Y-m-d');

require 'includes/header.php';
?>

<h1 class="page-title">Emprunts en cours</h1>

<?php if (isset($_GET['retour'])): ?>
    <p class="message message-ok">Retour enregistré.</p>
<?php endif; ?>

<?php if (count($emprunts) === 0): ?>

    <p class="lead">Aucun emprunt en cours.</p>

<?php else: ?>

    <div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Adhérent</th>
                <th>Livre</th>
                <th>Date d'emprunt</th>
                <th>Retour prévu</th>
                <th>État</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt): ?>

                <?php
                // On regarde si la date de retour prévue est déjà passée.
                $enRetard = false;
                if ($emprunt['date_retour_prevue'] < $aujourdhui) {
                    $enRetard = true;
                }
                ?>

                <tr class="<?php if ($enRetard) { echo 'row-late'; } ?>">
                    <td><?php echo htmlspecialchars($emprunt['nom'] . ' ' . $emprunt['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($emprunt['date_emprunt']))); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($emprunt['date_retour_prevue']))); ?></td>
                    <td>
                        <?php if ($enRetard): ?>
                            <span class="badge badge-late">EN RETARD</span>
                        <?php else: ?>
                            <span class="badge badge-out">En cours</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" action="retour.php">
                            <input type="hidden" name="id_emprunt" value="<?php echo $emprunt['id_emprunt']; ?>">
                            <button type="submit" class="btn btn-small">Retour</button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>
