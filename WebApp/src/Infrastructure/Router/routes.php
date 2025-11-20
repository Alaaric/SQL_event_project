<?php

use EventApp\Infrastructure\Controller\EventController;
use EventApp\Infrastructure\Controller\InscriptionController;
use EventApp\Infrastructure\Controller\MigrationController;

return function ($router) {
    $eventController = new EventController();
    $inscriptionController = new InscriptionController();
    $migrationController = new MigrationController();

    $router->get('list', fn() => $eventController->index());
    $router->get('migrate', fn() => $migrationController->migrate());

    $router->post('create_event', fn() => $eventController->create());
    $router->post('update_dates', fn() => $eventController->updateDates());
    $router->post('delete_event', fn() => $eventController->delete());
    $router->post('inscribe', fn() => $inscriptionController->create());
    $router->post('unsubscribe', fn() => $inscriptionController->delete());
};
