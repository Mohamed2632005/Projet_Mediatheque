<?php
require 'config/db.php';
$titrePage = 'Liste des adhérents';
$mainClass = 'page';
$messageErreur = '';

// Ce bloc ne s'exécute que quand le formulaire "Ajouter un adhérent" est envoyé.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);

    if ($nom === '' || $prenom === '' || $email === '') {
        $messageErreur = 'Veuillez remplir tous les champs.';
    } else {
        try {
            // On envoye la requete SQL mais avec des emplacements réservés (on dit ou)
            $ajout = $pdo->prepare(
                "INSERT INTO adherent (nom, prenom, email, date_inscription)
                 VALUES (:nom, :prenom, :email, CURDATE())"
            );
            // puis on le envoye (on dit quoi)
            $ajout->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
            ]);

            // Redirection pour éviter qu'un F5 recrée le même adhérent.
            header('Location: adherents.php?ok=1');
            exit;

        } catch (PDOException $e) {
            // L'e-mail est UNIQUE dans la base : si on en insère un qui existe déjà,
            // PDO lève une exception. On affiche un message clair au lieu de planter.
            $messageErreur = 'Cet e-mail est déjà utilisé par un autre adhérent.';
        }
    }
}

// Récupère tous les adhérents triés par nom/prénom puis l'affiche.
$sql = "SELECT nom, prenom, email, date_inscription
        FROM adherent
        ORDER BY nom, prenom";

$adherents = $pdo->query($sql)->fetchAll();

require 'includes/header.php';
?>

<h1 class="page-title">Liste des adhérents</h1>

<?php if (isset($_GET['ok'])): ?>
    <p class="message message-ok">Adhérent ajouté.</p>
<?php endif; ?>

<?php if ($messageErreur !== ''): ?>
    <p class="message message-erreur"><?php echo htmlspecialchars($messageErreur); ?></p>
<?php endif; ?>

<h2 class="section-title">Ajouter un adhérent</h2>
<form method="post" action="adherents.php" class="form">
    <div class="field">
        <label for="nom" class="field-label">Nom</label>
        <input type="text" name="nom" id="nom" class="input" required>
    </div>

    <div class="field">
        <label for="prenom" class="field-label">Prénom</label>
        <input type="text" name="prenom" id="prenom" class="input" required>
    </div>

    <div class="field">
        <label for="email" class="field-label">E-mail</label>
        <input type="email" name="email" id="email" class="input" required>
    </div>

    <button type="submit" class="btn">Ajouter</button>
</form>

<h2 class="section-title">Tous les adhérents</h2>
<div class="table-wrap">
<table class="table">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>E-mail</th>
            <th>Date d'inscription</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($adherents as $adherent): ?>
            <tr>
                <td><?php echo htmlspecialchars($adherent['nom']); ?></td>
                <td><?php echo htmlspecialchars($adherent['prenom']); ?></td>
                <td><?php echo htmlspecialchars($adherent['email']); ?></td>
                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($adherent['date_inscription']))); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php require 'includes/footer.php'; ?>
