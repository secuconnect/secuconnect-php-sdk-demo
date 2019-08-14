<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\Api\SmartDevicesApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\SmartDevicesDTO;

try {

    /*
     * The next will print for the first time:
     *
     * Your user code is: cryrs405
     * Your verification url is: https://www.secuoffice.com/
     */
    Authenticator::authenticateByClientCredentials(...[
        '...',
        '...',
    ]);

    $api_instance = new SmartDevicesApi();
    var_dump('Access-Token: ' . $api_instance->getApiClient()->getConfig()->getAccessToken());

    $smartDeviceDTO = new SmartDevicesDTO();
    $smartDeviceDTO->setMerchant("MRC_...");
    $smartDeviceDTO->setStore("STO_...");
    $smartDeviceDTO->setContract("GCR_..");
    $smartDeviceDTO->setType("cashier");
    $smartDeviceDTO->setVendor("...");
    $smartDeviceDTO->setVendorUid("/uuid/...");
    $smartDeviceDTO->setDescription("MerchantName");

    $response = $api_instance->addDevice($smartDeviceDTO);

    print_r($response);

} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
