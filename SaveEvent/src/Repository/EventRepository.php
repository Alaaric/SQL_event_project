<?php

namespace EventManager\Repository;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Exception\BulkWriteException;
use EventManager\Exception\DuplicateEventException;

class EventRepository
{
    private Collection $collection;

    public function __construct()
    {
        $host = $_ENV['MONGODB_URI'];
        $database = $_ENV['MONGODB_DATABASE'];

        $client = new Client($host);
        $this->collection = $client->$database->events;
        $this->createUniqueIndex();
    }

    /**
     * @throws DuplicateEventException
     */
    public function save(array $eventData): string
    {
        $hash = hash('sha256', json_encode($eventData));

        $document = $eventData;
        $document['_hash'] = $hash;

        try {
            $result = $this->collection->insertOne($document);
            return (string)$result->getInsertedId();
        } catch (BulkWriteException $e) {
            throw new DuplicateEventException("Event already exists with hash: $hash");
        }
    }

    private function createUniqueIndex(): void
    {
        $this->collection->createIndex(
            ['_hash' => 1],
            ['unique' => true, 'name' => 'hash_unique_index']
        );
    }
}
