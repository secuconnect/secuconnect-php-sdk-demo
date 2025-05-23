<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../vendor/autoload.php';

use Exception;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;

try {
    // Change environment to "testing" (which is the default in the SDK)
    Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secuconnect.com/api/v2');
    Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secuconnect.com/');

    // Change environment to "live"
//    Configuration::getDefaultConfiguration()->setHost('https://connect.secucard.com/api/v2');
//    Configuration::getDefaultConfiguration()->setAuthHost('https://connect.secucard.com/');

    $token = Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    echo 'Auth token: ' . $token;

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
