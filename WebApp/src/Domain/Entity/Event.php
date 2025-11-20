<?php

namespace EventApp\Domain\Entity;

readonly class Event
{
    public function __construct(
        public int $id,
        public string $nom,
        public \DateTime $dateCreation,
        public \DateTime $dateDebut,
        public \DateTime $dateFin,
        public int $personnesMaximum,
        public string $lieu
    ) {}
}
