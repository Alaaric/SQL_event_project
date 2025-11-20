<?php

namespace EventApp\Infrastructure\Controller;

use EventApp\Infrastructure\DTO\CreateEventDTO;
use EventApp\Infrastructure\Repository\MySQLEventRepository;
use EventApp\Infrastructure\Repository\MySQLInscriptionRepository;
use EventApp\Infrastructure\Database\DatabaseConnection;

class EventController
{
    private MySQLEventRepository $eventRepository;
    private MySQLInscriptionRepository $inscriptionRepository;

    public function __construct()
    {
        $pdo = DatabaseConnection::getInstance();
        $this->eventRepository = new MySQLEventRepository($pdo);
        $this->inscriptionRepository = new MySQLInscriptionRepository($pdo);
    }

    public function index(): array
    {
        $events = $this->eventRepository->findAll();
        $selectedEventId = $_GET['event_id'] ?? null;
        $inscriptions = $selectedEventId ? $this->inscriptionRepository->findByEventId((int)$selectedEventId) : [];

        return [
            'events' => $events,
            'selectedEventId' => $selectedEventId,
            'inscriptions' => $inscriptions
        ];
    }

    public function create(): void
    {
        $eventDTO = new CreateEventDTO(
            $_POST['nom'],
            new \DateTime($_POST['date_debut']),
            new \DateTime($_POST['date_fin']),
            (int)$_POST['personnes_maximum'],
            $_POST['lieu']
        );

        $this->eventRepository->create($eventDTO);
        $this->redirect('/');
    }

    public function updateDates(): void
    {
        $this->eventRepository->updateDates(
            (int)$_POST['id'],
            new \DateTime($_POST['date_debut']),
            new \DateTime($_POST['date_fin'])
        );
        $this->redirect('/');
    }

    public function delete(): void
    {
        $this->eventRepository->delete((int)$_POST['id']);
        $this->redirect('/');
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
