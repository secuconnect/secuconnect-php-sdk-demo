<?php
/** @noinspection PhpUnhandledExceptionInspection */

date_default_timezone_set('Europe/Berlin');

require_once __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Cache\FileCache;
use Secuconnect\Client\Configuration;

try {
    // Change environment to live / testing (default is "testing")
    Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secupay-ag.de/api/v2'); // demo server
//    Configuration::getDefaultConfiguration()->setHost('https://connect.secucard.com/api/v2'); // live

    Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secupay-ag.de/'); // demo server
//    Configuration::getDefaultConfiguration()->setAuthHost('https://connect.secucard.com/'); // live

    // Create logger
    // -> not needed anymore

    // Create cache storage
    Configuration::getDefaultConfiguration()->setCache(new FileCache(__DIR__ . '/.cache'));

    // Set credentials
    $token = Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    echo 'Auth token: ' . $token . "\n";

    /*
     * Sample output:
     * ==============
     * Auth token: dprfdtmuttqhm6k1omm7bnl7d0
     */
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
