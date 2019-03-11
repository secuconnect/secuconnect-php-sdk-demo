<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\STOMP\Comunication\StompComunicationController;

try {

    $x = new ReceivedFrameController();
    $x->process('test');
    define('RECEIVED_FRAME_CONTROLLER', ReceivedFrameController::class);


    /*
     * The next will print for the first time:
     *
     * Your user code is: cryrs405
     * Your verification url is: https://www.secuoffice.com/
     */
    Authenticator::authenticateByDevice(...        [
        '...',
        '...',
        '/vendor/.../uuid/...'
    ]);

    $StompComunicationController = new StompComunicationController();
    $StompComunicationController->run();
} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
