<?php

namespace EventManager;

use EventManager\Repository\EventRepository;
use EventManager\Exception\DuplicateEventException;

class EventManager
{
    private EventRepository $repository;

    public function __construct()
    {
        $this->repository = new EventRepository();
    }

    public function save(array $jsonData): array
    {
        try {
            $insertId = $this->repository->save($jsonData);
            return [
                'success' => true,
                'message' => "Event created successfully (ID: $insertId)"
            ];
        } catch (DuplicateEventException $e) {
            return [
                'success' => false,
                'message' => "Duplicate error: " . $e->getMessage()
            ];
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'message' => "Format validation error: " . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Unexpected error: " . $e->getMessage()
            ];
        }
    }

    public function process(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return ['success' => false, 'message' => "File not found: $filepath"];
        }

        $json = json_decode(file_get_contents($filepath), true);
        if (!$json) {
            return [
                'success' => false,
                'message' => "Invalid JSON in file: $filepath. Error: " . json_last_error_msg()
            ];
        }

        return $this->save($json);
    }
}
