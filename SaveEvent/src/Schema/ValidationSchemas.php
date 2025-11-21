<?php

namespace EventManager\Schema;

class ValidationSchemas
{
    public static function getEventSchema(): array
    {
        return [
            '$jsonSchema' => [
                'bsonType' => 'object',
                'additionalProperties' => true,
                /* On peu autoriser les 3 schemas dans la même collection avec le anyOf mais est-ce propre de faire ça? ¯\_(ツ)_/¯  */
                'anyOf' => [
                    self::getLiveTicketFormat(),
                    self::getTrueGisterFormat(),
                    self::getDISISFineFormat()
                ]
            ]
        ];
    }

    private static function getLiveTicketFormat(): array
    {
        return [
            'required' => ['event', 'start', 'end', 'max', 'where', 'attendees'],
            'properties' => [
                'event' => ['bsonType' => 'string', 'minLength' => 1],
                'start' => ['bsonType' => 'string'],
                'end' => ['bsonType' => 'string'],
                'max' => ['bsonType' => 'int', 'minimum' => 1],
                'where' => ['bsonType' => 'string'],
                'attendees' => [
                    'bsonType' => 'array',
                    'items' => [
                        'bsonType' => 'object',
                        'required' => ['fn', 'ln'],
                        'properties' => [
                            'fn' => ['bsonType' => 'string', 'minLength' => 1],
                            'ln' => ['bsonType' => 'string', 'minLength' => 1],
                            'when' => ['bsonType' => 'string']
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function getTrueGisterFormat(): array
    {
        return [
            'required' => ['results'],
            'properties' => [
                'results' => [
                    'bsonType' => 'array',
                    'minItems' => 1,
                    'items' => [
                        'bsonType' => 'object',
                        'required' => ['event', 'attendees'],
                        'properties' => [
                            'event' => [
                                'bsonType' => 'object',
                                'required' => ['event_name', 'event_begin', 'event_finish', 'event_where'],
                                'properties' => [
                                    'event_name' => ['bsonType' => 'string', 'minLength' => 1],
                                    'event_begin' => ['bsonType' => 'string'],
                                    'event_finish' => ['bsonType' => 'string'],
                                    'event_where' => ['bsonType' => 'string']
                                ]
                            ],
                            'attendees' => [
                                'bsonType' => 'array',
                                'items' => [
                                    'bsonType' => 'object',
                                    'required' => ['attendee_1', 'attendee_2'],
                                    'properties' => [
                                        'attendee_1' => ['bsonType' => 'string', 'minLength' => 1],
                                        'attendee_2' => ['bsonType' => 'string', 'minLength' => 1]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private static function getDISISFineFormat(): array
    {
        return [
            'required' => ['e_name', 'e_start', 'e_finish', 'e_location', 'e_attendees_max', 'attendees'],
            'properties' => [
                'e_name' => ['bsonType' => 'string', 'minLength' => 1],
                'e_start' => ['bsonType' => 'string'],
                'e_finish' => ['bsonType' => 'string'],
                'e_location' => ['bsonType' => 'string'],
                'e_attendees_max' => ['bsonType' => 'int', 'minimum' => 1],
                'attendees' => ['bsonType' => 'string']
            ]
        ];
    }
}
