<?php


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO(
    "mysql:host={$_ENV['MYSQL_HOST']};dbname={$_ENV['MYSQL_DB']};charset=utf8mb4",
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASS'],
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
);

$action = $_POST["action"];

switch ($action) {
    case "creer":
        $stmt = $pdo->prepare("CALL creer_evenement(?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST["nom"],
            $_POST["date_debut"],
            $_POST["date_fin"],
            $_POST["max"],
            $_POST["lieu"]
        ]);
        $msg = "Événement créé.";
        break;

    case "inscrire":
        $stmt = $pdo->prepare("CALL inscrire_personne(?, ?, ?)");
        $stmt->execute([
            $_POST["id"],
            $_POST["prenom"],
            $_POST["nom"]
        ]);
        $msg = "Personne inscrite.";
        break;

    case "desinscrire":
        $stmt = $pdo->prepare("CALL desinscrire_personne(?, ?)");
        $stmt->execute([
            $_POST["prenom"],
            $_POST["nom"]
        ]);
        $msg = "Personne désinscrite.";
        break;

    case "supprimer":
        $stmt = $pdo->prepare("CALL supprimer_evenement(?)");
        $stmt->execute([ $_POST["id"] ]);
        $msg = "Événement supprimé.";
        break;

    case "changerdates":
        $stmt = $pdo->prepare("CALL changer_dates_evenement(?, ?, ?)");
        $stmt->execute([
            $_POST["id"],
            $_POST["date_debut"],
            $_POST["date_fin"]
        ]);
        $msg = "Dates modifiées.";
        break;

    default:
        $msg = "Action inconnue.";
}

echo "<script>alert('$msg'); window.location='index.html';</script>";
