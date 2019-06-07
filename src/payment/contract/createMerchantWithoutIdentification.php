<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentContractsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\PaymentContractsDTOClone;
use Secuconnect\Client\Model\PaymentInformation;

try {

    // Authenticate against the API
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new PaymentContractsApi();

    /*
     * bank account of the merchant to get the money
     */
    $payout_account = new PaymentInformation();
    $payout_account->setIban("DE89370400440532013000");
    $payout_account->setBic(""); // recommended
    $payout_account->setOwner("Test #1");

    /*
     * Submitting the data to the API
     */
    $request_data = new PaymentContractsDTOClone();
    $request_data->setPaymentData($payout_account);
    $request_data->setPayinAccount(false);
    $request_data->setProject("project_name " . time()); // must be unique for each request

    $response = $api_instance->callClone('me', $request_data); //"me" is a shortcut for the current contract of the API-user

    print_r($response);

} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
