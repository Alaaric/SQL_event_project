<?php

namespace EventApp\Infrastructure\Migration\Normalizers;

interface EventNormalizerInterface
{
    public function canNormalize(array $eventData): bool;
    public function normalize(array $eventData): array;
}
