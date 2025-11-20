<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Événements</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<h1>Gestion des Événements</h1>

<div class="container">

    <!-- Créer un événement -->
    <div class="card">
        <h2>Créer un événement</h2>
        <form action="actions.php" method="POST">
            <input type="hidden" name="action" value="creer">

            <label>Nom :</label>
            <input type="text" name="nom" required>

            <label>Date début :</label>
            <input type="datetime-local" name="date_debut" required>

            <label>Date fin :</label>
            <input type="datetime-local" name="date_fin" required>

            <label>Personnes maximum :</label>
            <input type="number" name="max" required>

            <label>Lieu :</label>
            <input type="text" name="lieu" required>

            <button type="submit">Créer</button>
        </form>
    </div>

    <!-- Inscrire une personne -->
    <div class="card">
        <h2>Inscrire une personne</h2>
        <form action="actions.php" method="POST">
            <input type="hidden" name="action" value="inscrire">

            <label>ID Événement :</label>
            <input type="number" name="id" required>

            <label>Prénom :</label>
            <input type="text" name="prenom" required>

            <label>Nom :</label>
            <input type="text" name="nom" required>

            <button type="submit">Inscrire</button>
        </form>
    </div>

    <!-- Désinscrire une personne -->
    <div class="card">
        <h2>Désinscrire une personne</h2>
        <form action="actions.php" method="POST">
            <input type="hidden" name="action" value="desinscrire">

            <label>Prénom :</label>
            <input type="text" name="prenom" required>

            <label>Nom :</label>
            <input type="text" name="nom" required>

            <button type="submit" class="danger">Désinscrire</button>
        </form>
    </div>

    <!-- Supprimer un événement -->
    <div class="card">
        <h2>Supprimer un événement</h2>
        <form action="actions.php" method="POST">
            <input type="hidden" name="action" value="supprimer">

            <label>ID Événement :</label>
            <input type="number" name="id" required>

            <button type="submit" class="danger">Supprimer</button>
        </form>
    </div>

    <!-- Changer les dates -->
    <div class="card">
        <h2>Changer dates</h2>
        <form action="actions.php" method="POST">
            <input type="hidden" name="action" value="changerdates">

            <label>ID Événement :</label>
            <input type="number" name="id" required>

            <label>Nouvelle date début :</label>
            <input type="datetime-local" name="date_debut" required>

            <label>Nouvelle date fin :</label>
            <input type="datetime-local" name="date_fin" required>

            <button type="submit">Changer</button>
        </form>
    </div>

</div>
</body>
</html>
