<?php

namespace EventApp\Infrastructure\Repository;

use EventApp\Domain\Entity\Inscription;
use EventApp\Domain\Interfaces\InscriptionRepositoryInterface;
use EventApp\Infrastructure\DTO\CreateInscriptionDTO;
use PDO;

class MySQLInscriptionRepository implements InscriptionRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inscriptions WHERE evenement_id = ? ORDER BY nom, prenom');
        $stmt->execute([$eventId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Inscription(
            $row['id'],
            $row['evenement_id'],
            $row['prenom'],
            $row['nom'],
            new \DateTime($row['date_inscription'])
        ), $results);
    }

    public function create(CreateInscriptionDTO $inscriptionDTO): Inscription
    {
        $stmt = $this->pdo->prepare('CALL InscrirePersonne(?, ?, ?)');
        $stmt->execute([$inscriptionDTO->evenementId, $inscriptionDTO->prenom, $inscriptionDTO->nom]);

        $stmt = $this->pdo->prepare('
            SELECT * FROM inscriptions 
            WHERE evenement_id = ? AND prenom = ? AND nom = ? 
            ORDER BY date_inscription DESC 
            LIMIT 1
        ');
        $stmt->execute([$inscriptionDTO->evenementId, $inscriptionDTO->prenom, $inscriptionDTO->nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Inscription(
            $row['id'],
            $row['evenement_id'],
            $row['prenom'],
            $row['nom'],
            new \DateTime($row['date_inscription'])
        );
    }

    public function delete(int $inscriptionId): void
    {
        $stmt = $this->pdo->prepare('CALL DesinscrirePersonne(?)');
        $stmt->execute([$inscriptionId]);
    }
}
