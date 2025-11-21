<?php

namespace EventManager\Repository;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use EventManager\Exception\DuplicateEventException;
use EventManager\Schema\ValidationSchemas;

class EventRepository
{
    private const string COLLECTION_NAME = 'events';
    private Collection $collection;

    public function __construct()
    {
        $host = $_ENV['MONGODB_URI'];
        $database = $_ENV['MONGODB_DATABASE'];

        $client = new Client($host);
        $db = $client->$database;

        if (!$this->collectionExists($db)) {
            $this->createCollection($db);
        }
        $this->collection = $db->selectCollection(self::COLLECTION_NAME);
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
        } catch (\Exception $e) {
            $code = $e->getCode();

            /** On peut pas differencier les catch car nous avons dans ces 2 cas une BulkWriteException, on peu se fier que au code de retour */

            if ($code === 11000) {
                throw new DuplicateEventException("Event already exists with hash: $hash");
            }

            if ($code === 121) {
                throw new \InvalidArgumentException("Document validation failed: " . $e->getMessage());
            }

            throw new \RuntimeException("Write operation in database failed: " . $e->getMessage());
        }
    }

    private function createCollection(Database $db): void
    {
        $db->createCollection(self::COLLECTION_NAME, [
            'validator' => ValidationSchemas::getEventSchema(),
            'validationLevel' => 'strict',
            'validationAction' => 'error'
        ]);
    }

    private function collectionExists(Database $db): bool
    {
        foreach ($db->listCollectionNames() as $name) {
            if ($name === self::COLLECTION_NAME) {
                return true;
            }
        }
        return false;
    }

    private function createUniqueIndex(): void
    {
        $this->collection->createIndex(
            ['_hash' => 1],
            ['unique' => true, 'name' => 'hash_unique_index']
        );
    }
}
