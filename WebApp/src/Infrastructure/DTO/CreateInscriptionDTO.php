<?php

namespace EventApp\Infrastructure\DTO;

readonly class CreateInscriptionDTO
{
    public function __construct(
        public int $evenementId,
        public string $prenom,
        public string $nom
    ) {}
}
