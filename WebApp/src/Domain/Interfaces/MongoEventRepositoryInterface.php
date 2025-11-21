<?php

namespace EventApp\Domain\Interfaces;

interface MongoEventRepositoryInterface
{
    /**
     * @return array[]
     */
    public function findAllRawEvents(): array;
}
