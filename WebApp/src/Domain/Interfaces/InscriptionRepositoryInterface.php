<?php

namespace EventApp\Domain\Interfaces;

use EventApp\Domain\Entity\Inscription;
use EventApp\Infrastructure\DTO\CreateInscriptionDTO;

interface InscriptionRepositoryInterface
{
    public function findByEventId(int $eventId): array;
    public function create(CreateInscriptionDTO $inscriptionDTO, ?string $dateInscription = null): Inscription;
    public function delete(int $inscriptionId): void;
}
