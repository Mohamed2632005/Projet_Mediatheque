# Médiathèque — Mini-projet BTS CIEL IR

Application Web de gestion de médiathèque en HTML, CSS, PHP (PDO) et MariaDB.
Elle permet au personnel de consulter les livres et les adhérents, d'enregistrer des emprunts et des retours, sans passer par phpMyAdmin.

## Travaille fait par

- Mohamed Toubi

## Guide d'installation pour le Projet 

1. Installer XAMPP, puis démarrer Apache et MySQL.
2. Dans phpMyAdmin, créer une base `mediatheque` (interclassement `utf8mb4_general_ci`).
3. Importer le fichier SQL fourni (onglet Importer).
4. Copier le dossier `mediatheque` dans `C:\xampp\htdocs\`.
5. Ouvrir `http://localhost/mediatheque/`.

Paramètres de connexion (`config/db.php`) : serveur `127.0.0.1`, utilisateur `root`, sans mot de passe (configuration locale XAMPP).

## Arborescence

mediatheque/
├── index.php          Accueil + tableau de bord
├── livres.php         Liste des livres + recherche (titre, auteur, catégorie)
├── adherents.php       Liste des adhérents + ajout d'un adhérent
├── emprunts.php        Emprunts en cours + retards
├── emprunter.php        Nouvel emprunt
├── retour.php          Traitement du retour d'un livre
├── config/db.php        Connexion PDO
├── includes/
│   ├── header.php     Début de page + menu commun
│   └── footer.php     Fin de page
└── assets/style.css   Feuille de style unique

## Fonctionnalités terminées

- F01 — Accueil et menu de navigation commun à toutes les pages
- F02 — Liste des livres : titre, auteur(s), catégorie, année, ISBN, disponibilité
- F03 — Recherche de livres par titre
- F04 — Liste des adhérents : nom, prénom, e-mail, date d'inscription
- F05 — Nouvel emprunt : seuls les livres disponibles sont proposés, date du jour, retour prévu à J+14
- F06 — Emprunts en cours (`date_retour IS NULL`)
- F07 — Retards : mention « EN RETARD » avec mise en évidence CSS
- F08 — Retour d'un livre : `date_retour` renseignée et livre de nouveau disponible

## Bonus réalisés

- **Ajouter un adhérent** depuis l'application (page `adherents.php`), avec vérification de l'e-mail en double.
- **Tableau de bord** sur l'accueil : nombre de livres, d'adhérents, d'emprunts en cours et de retards (4 requêtes `COUNT`).
- **Recherche multicritère** : la recherche de `livres.php` porte maintenant sur le titre, la catégorie et le nom/prénom des auteurs.

## Fonctionnalités non terminées

- Bonus non réalisés : ajout/modification d'un livre, pagination de la liste des livres. (j'ai pas eu le temp de faire les fonctionalités Bonus j'en ai fait 3/5)

## Choix techniques

- **PDO** avec requêtes préparées dès qu'une donnée saisie par l'utilisateur entre dans une requête (recherche, emprunt, retour, ajout d'adhérent) : la requête et la donnée voyagent séparément, (sa nprotège contre l'injection SQL.)
- **htmlspecialchars()** on la utiliser sur toutes les données affichées, (pour se protéger de l'injection de code HTML/JavaScript (XSS)).
- **Vérification avant emprunt** : on relit `disponible` juste avant d'enregistrer l'emprunt, (pour éviter qu'un livre déjà pris soit emprunté une deuxième fois).
- **Vérification avant retour** : on ne traite un retour que si `date_retour` est encore vide (`IS NULL`), (pour éviter un double retour).
- **Redirection après un formulaire POST** (`header('Location: ...')` puis `exit`) : évite qu'un rechargement de page (F5) recrée le même emprunt, le même retour ou le même adhérent.
- Une deuxième requête par livre pour récupérer ses auteurs (page `livres.php`), plutôt qu'une seule requête à plusieurs jointures (c'est plus simple à lire et à expliquer).
- La recherche multicritère utilise une sous-requête (`IN (SELECT ...)`) sur `livre_auteur` et `auteur` pour retrouver les livres d'un auteur donné, en plus du titre et de la catégorie.
- **try/catch** autour de l'ajout d'un adhérent, pour intercepter l'erreur si l'e-mail (contrainte `UNIQUE`) existe déjà, et afficher un message clair.
- Méthode GET pour la recherche (elle ne modifie rien), méthode POST pour les actions qui modifient la base (emprunt, retour, ajout d'adhérent).

## Limites connues

- On a pas d'authentification du personnel.
- Dans la recherche, les caractères `%` et `_` agissent comme des jokers du `LIKE`.
- Identifiants de base `root` sans mot de passe : vu que c'est juste un projet je n'ai pas mis.