<?php

namespace EventApp\Domain\Entity;

readonly class Inscription
{
    public function __construct(
        public int $id,
        public int $evenementId,
        public string $prenom,
        public string $nom,
        public \DateTime $dateInscription
    ) {}
}
