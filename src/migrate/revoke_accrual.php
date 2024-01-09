<?php
/** @noinspection PhpUnhandledExceptionInspection */

require __DIR__ . '/init.php';

use Secuconnect\Client\Api\GeneralContractsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Model\GetAvailablePaymentMethodsDTO;

try {
    (new GeneralContractsApi())->revokeAccrual(
        'GCR_8PBB9DXYS7A7V8CP3G6PM4G9W20JO2'
    );

    echo 'The background process to revoke the accrual flag, for all transactions of this contract, was successfully started.' . "\n";
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
