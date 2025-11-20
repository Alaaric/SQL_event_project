<?php

namespace EventApp\Infrastructure\Repository;

use EventApp\Domain\Entity\Event;
use EventApp\Domain\Interfaces\EventRepositoryInterface;
use EventApp\Infrastructure\DTO\CreateEventDTO;
use PDO;

class MySQLEventRepository implements EventRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM evenements ORDER BY date_debut');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Event(
            $row['id'],
            $row['nom'],
            new \DateTime($row['date_creation']),
            new \DateTime($row['date_debut']),
            new \DateTime($row['date_fin']),
            $row['personnes_maximum'],
            $row['lieu']
        ), $results);
    }

    public function findById(int $id): ?Event
    {
        $stmt = $this->pdo->prepare('SELECT * FROM evenements WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Event(
            $row['id'],
            $row['nom'],
            new \DateTime($row['date_creation']),
            new \DateTime($row['date_debut']),
            new \DateTime($row['date_fin']),
            $row['personnes_maximum'],
            $row['lieu']
        ) : null;
    }

    public function create(CreateEventDTO $eventDTO): Event
    {
        $stmt = $this->pdo->prepare('CALL CreerEvenement(?, ?, ?, ?, ?, @evenement_id)');
        $stmt->execute([
            $eventDTO->nom,
            $eventDTO->dateDebut->format('Y-m-d H:i:s'),
            $eventDTO->dateFin->format('Y-m-d H:i:s'),
            $eventDTO->personnesMaximum,
            $eventDTO->lieu
        ]);

        $result = $this->pdo->query('SELECT @evenement_id as id');
        $eventId = $result->fetch()['id'];

        return new Event(
            $eventId,
            $eventDTO->nom,
            new \DateTime(),
            $eventDTO->dateDebut,
            $eventDTO->dateFin,
            $eventDTO->personnesMaximum,
            $eventDTO->lieu
        );
    }

    public function updateDates(int $id, \DateTime $dateDebut, \DateTime $dateFin): void
    {
        $stmt = $this->pdo->prepare('CALL ModifierDatesEvenement(?, ?, ?)');
        $stmt->execute([
            $id,
            $dateDebut->format('Y-m-d H:i:s'),
            $dateFin->format('Y-m-d H:i:s')
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('CALL SupprimerEvenement(?)');
        $stmt->execute([$id]);
    }
}
