<?php

namespace EventApp\Domain\Interfaces;

use EventApp\Domain\Entity\Inscription;
use EventApp\Infrastructure\DTO\CreateInscriptionDTO;

interface InscriptionRepositoryInterface
{
    public function findByEventId(int $eventId): array;
    public function create(CreateInscriptionDTO $inscriptionDTO): Inscription;
    public function createWithCustomDate(int $eventId, string $prenom, string $nom, string $dateInscription): Inscription;
    public function delete(int $inscriptionId): void;
}
