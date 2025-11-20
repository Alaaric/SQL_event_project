<?php

namespace EventApp\Infrastructure\Controller;

use EventApp\Infrastructure\DTO\CreateInscriptionDTO;
use EventApp\Infrastructure\Repository\MySQLInscriptionRepository;
use EventApp\Infrastructure\Database\DatabaseConnection;

class InscriptionController
{
    private MySQLInscriptionRepository $inscriptionRepository;

    public function __construct()
    {
        $pdo = DatabaseConnection::getInstance();
        $this->inscriptionRepository = new MySQLInscriptionRepository($pdo);
    }

    public function create(): void
    {
        $inscriptionDTO = new CreateInscriptionDTO(
            (int)$_POST['evenement_id'],
            $_POST['prenom'],
            $_POST['nom']
        );

        $this->inscriptionRepository->create($inscriptionDTO);
        $this->redirect('/');
    }

    public function delete(): void
    {
        $this->inscriptionRepository->delete((int)$_POST['inscription_id']);
        $this->redirect('/');
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
