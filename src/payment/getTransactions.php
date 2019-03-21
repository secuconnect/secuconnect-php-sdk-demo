<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;

try {

    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new \Secuconnect\Client\Api\PaymentTransactionsApi();

    $response = $api_instance->paymentTransactionsGet();

    print_r($response);

} catch (ApiException $e) {

    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
