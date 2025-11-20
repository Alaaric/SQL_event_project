<?php


require_once __DIR__ . '/../vendor/autoload.php';

use EventManager\Repository\EventRepository;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// MySQL
$pdo = new PDO(
    "mysql:host={$_ENV['localhost']};dbname={$_ENV['event_management']};charset=utf8mb4",
    $_ENV['root'],
    $_ENV[''],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Mongo
$repo = new EventRepository();
$events = $repo->findAll();

foreach ($events as $event) {

    // 1) Créer l'événement
    $stmt = $pdo->prepare("CALL creer_evenement(?, ?, ?, ?, ?)");
    $stmt->execute([
        $event["nom"],
        $event["date_debut"],
        $event["date_fin"],
        $event["max"],
        $event["lieu"]
    ]);

    // ID MySQL du nouvel événement
    $eventId = $pdo->lastInsertId();

    // 2) Inscrire les personnes
    foreach ($event["attendees"] as $a) {
        $stmt = $pdo->prepare("CALL inscrire_personne(?, ?, ?)");
        $stmt->execute([
            $eventId,
            $a["prenom"],
            $a["nom"]
        ]);
    }
}

echo "Import MongoDB → MySQL terminé.";
