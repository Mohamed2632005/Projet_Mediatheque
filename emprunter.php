<?php
require 'config/db.php';
$titrePage = 'Nouvel emprunt';
$mainClass = 'page';
$messageErreur = '';

// Ce bloc ne s'exécute que quand le formulaire a été envoyé (bouton cliqué).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // récupère l'adhérent et le livre choisis
    $idAdherent = $_POST['id_adherent'];
    $idLivre = $_POST['id_livre'];

    // affiche une erreur si l'un des deux champs est vide
    if ($idAdherent === '' || $idLivre === '') {
        $messageErreur = 'Veuillez choisir un adhérent et un livre.';
    } else {

        // Avant d'enregistrer l'emprunt, on vérifie que le livre est toujours disponible.
        $verif = $pdo->prepare("SELECT disponible FROM livre WHERE id_livre = :id_livre");
        $verif->execute([':id_livre' => $idLivre]);
        $livre = $verif->fetch();

        // Si le livre n'existe pas ou n'est plus disponible
        if (!$livre || $livre['disponible'] != 1) {
            $messageErreur = "Ce livre n'est plus disponible.";
        } else {

            // 1. On enregistre l'emprunt.
            // CURDATE() = date du jour, côté base de données.
            // Crée l'emprunt via une requête préparée
            $ajout = $pdo->prepare(
                "INSERT INTO emprunt (id_adherent, id_livre, date_emprunt, date_retour_prevue)
                 VALUES (:id_adherent, :id_livre, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))"
            );

            // la suite on envoye
            $ajout->execute([
                ':id_adherent' => $idAdherent,
                ':id_livre' => $idLivre,
            ]);

            // 2. On marque le livre comme non disponible.
            $maj = $pdo->prepare("UPDATE livre SET disponible = 0 WHERE id_livre = :id_livre");
            $maj->execute([':id_livre' => $idLivre]);

            // On redirige vers la même page avec ?ok=1 dans l'adresse.
            // Cela évite qu'un rechargement de la page (F5) crée un deuxième emprunt.
            header('Location: emprunter.php?ok=1');
            exit;
        }
    }
}

// Liste des adhérents, pour remplir la liste déroulante du formulaire.
$adherents = $pdo->query("SELECT id_adherent, nom, prenom FROM adherent ORDER BY nom, prenom")->fetchAll();

// Liste des livres disponibles uniquement : un livre déjà emprunté ne doit pas être proposé.
$livresDisponibles = $pdo->query("SELECT id_livre, titre FROM livre WHERE disponible = 1 ORDER BY titre")->fetchAll();

require 'includes/header.php';
?>

<h1 class="page-title">Nouvel emprunt</h1>

<?php if (isset($_GET['ok'])): ?>
    <p class="message message-ok">Emprunt enregistré. Retour prévu dans 14 jours.</p>
<?php endif; ?>

<?php if ($messageErreur !== ''): ?>
    <p class="message message-erreur"><?php echo htmlspecialchars($messageErreur); ?></p>
<?php endif; ?>

<?php if (count($livresDisponibles) === 0): ?>

    <p class="lead">Aucun livre n'est disponible actuellement.</p>

<?php else: ?>

    <form method="post" action="emprunter.php" class="form">

        <div class="field">
            <label for="id_adherent" class="field-label">Adhérent</label>
            <select name="id_adherent" id="id_adherent" class="input" required>
                <option value="">-- Choisir un adhérent --</option>
                <?php foreach ($adherents as $adherent): ?>
                    <option value="<?php echo $adherent['id_adherent']; ?>">
                        <?php echo htmlspecialchars($adherent['nom'] . ' ' . $adherent['prenom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="id_livre" class="field-label">Livre disponible</label>
            <select name="id_livre" id="id_livre" class="input" required>
                <option value="">-- Choisir un livre --</option>
                <?php foreach ($livresDisponibles as $livre): ?>
                    <option value="<?php echo $livre['id_livre']; ?>">
                        <?php echo htmlspecialchars($livre['titre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Enregistrer l'emprunt</button>

    </form>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>
