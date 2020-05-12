<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentSecupayDebitsApi;
use Secuconnect\Client\Api\PaymentSecupayPrepaysApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    // For prepay
    $api_instance = new PaymentSecupayPrepaysApi();
    $response = $api_instance->cancelPaymentTransactionById('secupayprepays', 'wejdmnkastmd4200084', null);

    print_r($response);

    // for direct debit
    $api_instance = new PaymentSecupayDebitsApi();
    $response = $api_instance->cancelPaymentTransactionById('secupaydebits', 'wwyavlwspqby4200085', null);

    print_r($response);
} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
