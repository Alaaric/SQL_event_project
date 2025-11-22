<?php

namespace EventApp\Infrastructure\Repository;

use EventApp\Domain\Entity\Inscription;
use EventApp\Domain\Exception\EventFullException;
use EventApp\Domain\Exception\PersonAlreadyRegisteredException;
use EventApp\Domain\Interfaces\InscriptionRepositoryInterface;
use EventApp\Infrastructure\DTO\CreateInscriptionDTO;
use PDO;
use PDOException;

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

    public function create(CreateInscriptionDTO $inscriptionDTO, ?string $dateInscription = null): Inscription
    {
        try {
            $stmt = $this->pdo->prepare('CALL InscrirePersonne(?, ?, ?, ?)');
            $stmt->execute([$inscriptionDTO->evenementId, $inscriptionDTO->prenom, $inscriptionDTO->nom, $dateInscription]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return new Inscription(
                $row['id'],
                $row['evenement_id'],
                $row['prenom'],
                $row['nom'],
                new \DateTime($row['date_inscription'])
            );
        } catch (PDOException $e) {
            if ($e->getCode() == '45001') {
                throw new EventFullException("L'événement a atteint le nombre maximum de participants");
            }
            if ($e->getCode() == '45002') {
                throw new PersonAlreadyRegisteredException("Personne déjà inscrite à cet événement");
            }
            throw $e;
        }
    }

    public function delete(int $inscriptionId): void
    {
        $stmt = $this->pdo->prepare('SELECT prenom, nom FROM inscriptions WHERE id = ?');
        $stmt->execute([$inscriptionId]);
        $inscription = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare('CALL DesinscrirePersonne(?, ?)');
        $stmt->execute([$inscription['prenom'], $inscription['nom']]);
    }
}
