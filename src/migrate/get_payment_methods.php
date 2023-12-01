<?php
/** @noinspection PhpUnhandledExceptionInspection */

require __DIR__ . '/init.php';

use Secuconnect\Client\Api\GeneralContractsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Model\GetAvailablePaymentMethodsDTO;

try {
    $payment_methods = (new GeneralContractsApi())->getAvailablePaymentMethods(
        'GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0',
        new GetAvailablePaymentMethodsDTO([
            'currency' => 'EUR',
            'is_demo' => true
        ])
    );

    if ($payment_methods) {
        echo 'Available payment methods for this contract: ' . json_encode($payment_methods, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo 'No (demo) payment methods are enabled for this contract.' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Available payment methods for this contract: [
     *     "Creditcard",
     *     "Debit",
     *     "easyCredit",
     *     "eps",
     *     "Invoice",
     *     "Paypal",
     *     "Prepay",
     *     "Sofort",
     *     "Applepay",
     *     "Googlepay"
     * ]
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
