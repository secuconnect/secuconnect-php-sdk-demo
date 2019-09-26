<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\Api\SmartDevicesApi;
use Secuconnect\Client\Api\SmartRoutingsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;
use Secuconnect\Client\Model\SmartDevicesDTO;
use Secuconnect\Client\Model\SmartRoutingsDTO;

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

    $api_instance = new SmartRoutingsApi();
    var_dump('Access-Token: ' . $api_instance->getApiClient()->getConfig()->getAccessToken());

    $smartRoutingsDTO = new SmartRoutingsDTO();
    $smartRoutingsDTO->setStore('STO_...');
    $smartRoutingsDTO->setDescription('TestRouting');

    $response = $api_instance->addRouting($smartRoutingsDTO);

    print_r($response);

} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
