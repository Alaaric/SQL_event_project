<?php

namespace EventApp\Infrastructure\DTO;

readonly class CreateEventDTO
{
    public function __construct(
        public string $nom,
        public \DateTime $dateDebut,
        public \DateTime $dateFin,
        public int $personnesMaximum,
        public string $lieu
    ) {}
}
