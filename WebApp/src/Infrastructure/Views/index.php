<!DOCTYPE html>
<html>

<head>
    <title>Gestion d'Événements</title>
</head>

<body>
    <h1>Gestion d'Événements</h1>

    <?php if (isset($migrated)): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; border-radius: 5px;">
            <h2>Migration OK</h2>
            <p><strong>Événements migrés:</strong> <?= $migrated ?></p>
            <?php if (!empty($errors)): ?>
                <p><strong>Erreurs:</strong> <?= implode(', ', $errors) ?></p>
            <?php endif; ?>
            <p><a href="?">Retour à la liste des événements</a></p>
        </div>
    <?php else: ?>
        <p><a href="?action=migrate">Migrer depuis MongoDB</a></p>
    <?php endif; ?>

    <h2>Créer un événement</h2>
    <form method="post" action="?action=create_event">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="datetime-local" name="date_debut" required>
        <input type="datetime-local" name="date_fin" required>
        <input type="number" name="personnes_maximum" placeholder="Max personnes (optionnel)">
        <input type="text" name="lieu" placeholder="Lieu" required>
        <button type="submit">Créer</button>
    </form>

    <?php if (isset($events)): ?>
        <h2>Événements (<?= count($events) ?>)</h2>
        <?php foreach ($events as $event): ?>
            <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <h3><?= htmlspecialchars($event->nom) ?></h3>
                <p> <?= $event->dateDebut->format('Y-m-d H:i') ?> → <?= $event->dateFin->format('Y-m-d H:i') ?></p>
                <p> <?= htmlspecialchars($event->lieu) ?> (Max: <?= $event->personnesMaximum ?? 'non renseigné' ?>)</p>

                <form method="post" action="?action=update_dates" style="display: inline;">
                    <input type="hidden" name="id" value="<?= $event->id ?>">
                    <input type="datetime-local" name="date_debut" value="<?= $event->dateDebut->format('Y-m-d\TH:i') ?>">
                    <input type="datetime-local" name="date_fin" value="<?= $event->dateFin->format('Y-m-d\TH:i') ?>">
                    <button type="submit">Modifier dates</button>
                </form>

                <form method="post" action="?action=delete_event" style="display: inline;">
                    <input type="hidden" name="id" value="<?= $event->id ?>">
                    <button type="submit" onclick="return confirm('Supprimer ?')"> Supprimer</button>
                </form>

                <a href="?event_id=<?= $event->id ?>">Gérer inscriptions</a>
            </div>
        <?php endforeach; ?>

        <?php if ($selectedEventId): ?>
            <?php $selectedEvent = null; ?>
            <?php foreach ($events as $e) {
                if ($e->id == $selectedEventId) {
                    $selectedEvent = $e;
                    break;
                }
            } ?>

            <h2>Inscriptions pour "<?= htmlspecialchars($selectedEvent->nom) ?>"</h2>

            <form method="post" action="?action=inscribe">
                <input type="hidden" name="evenement_id" value="<?= $selectedEventId ?>">
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" placeholder="Nom" required>
                <button type="submit">Inscrire</button>
            </form>

            <h3>Participants (<?= count($inscriptions) ?>)</h3>
            <?php foreach ($inscriptions as $inscription): ?>
                <div style="border: 1px solid #eee; margin: 5px; padding: 5px;">
                    <?= htmlspecialchars($inscription->prenom) ?> <?= htmlspecialchars($inscription->nom) ?>
                    <em>(<?= $inscription->dateInscription->format('Y-m-d') ?>)</em>
                    <form method="post" action="?action=unsubscribe" style="display: inline;">
                        <input type="hidden" name="inscription_id" value="<?= $inscription->id ?>">
                        <button type="submit">supprimer</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <p><a href="?">Retour aux événements</a></p>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>