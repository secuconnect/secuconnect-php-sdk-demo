<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;

try {
    // create a transaction (will fill the $stx_id with something like "STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK")
    require __DIR__ . '/prepay.php';

    if (!empty($stx_id)) {
        $prepay = (new SmartTransactionsApi())->cancelTransaction($stx_id);
        if ($prepay->getStatus() === 'cancelled') {
            echo 'Canceled secupay prepay transaction with id: ' . $prepay->getId() . "\n";
            echo 'Prepay data: ' . print_r($prepay->__toString(), true) . "\n";
        } else {
            echo 'Prepay cancellation failed' . "\n";
            exit;
        }
    } else {
        echo 'Prepay cancellation skipped because of missing ID' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Canceled secupay prepay transaction with id: STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK
     * Prepay data: {
     *     "status": "cancelled",
     *     ...
     * }
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
