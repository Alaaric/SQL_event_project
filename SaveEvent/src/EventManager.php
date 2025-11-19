<?php

namespace EventManager;

use EventManager\Repository\EventRepository;
use EventManager\Exception\DuplicateEventException;

class EventManager
{

    public function __construct(
        private EventRepository $repository = new EventRepository()
    ) {}

    public function save(array $jsonData): array
    {
        try {
            $this->repository->save($jsonData);
            return ['success' => true, 'message' => "Event created"];
        } catch (DuplicateEventException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function process(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => "File not found: $filepath"];
        }

        $json = json_decode(file_get_contents($filepath), true);
        if (!$json) {
            return ['success' => false, 'message' => "Invalid JSON: $filepath"];
        }

        return $this->save($json);
    }
}
