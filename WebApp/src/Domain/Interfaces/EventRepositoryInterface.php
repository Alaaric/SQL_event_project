<?php

namespace EventApp\Domain\Interfaces;

use EventApp\Domain\Entity\Event;
use EventApp\Infrastructure\DTO\CreateEventDTO;

interface EventRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?Event;
    public function create(CreateEventDTO $eventDTO): Event;
    public function updateDates(int $id, \DateTime $dateDebut, \DateTime $dateFin): void;
    public function delete(int $id): void;
}
