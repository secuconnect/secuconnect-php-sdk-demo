<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;

try {
    Authenticator::authenticateByClientCredentials(...[
        '...',
        '...'
    ]);

    $api_instance = new PaymentTransactionsApi();
    $response = $api_instance->revokeAccrual('PCI_TA6Y6JTJGE2GJ29XJ5V23S4A28D9NZ');

    print_r($response);
} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
