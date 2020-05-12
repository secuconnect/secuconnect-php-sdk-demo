<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentContainersApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\BankAccountDescriptor;
use Secuconnect\Client\Model\PaymentContainersDTO;
use Secuconnect\Client\Model\PaymentContainersDTOCustomer;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    // Build request objects
    $container = new PaymentContainersDTO();
    $container
        ->setType('bank_account') // optional "bank_account"
        ->setCustomer(
            new PaymentContainersDTOCustomer(
                [
                    'id' => 'PCU_...'
                ]
            )
        )  // from "src/payment/customer/createCustomer.php"
        ->setPrivate(
            new BankAccountDescriptor(
                [
                    'owner' => "John Doe",
                    'iban' => 'DE37503240001000000524',
                    'bic' => 'FTSBDEFAXXX'
                ]
            )
        );

    $api_instance = new PaymentContainersApi();
    $response = $api_instance->paymentContainersPost($container);

    print_r($response);
    /*
     * Secuconnect\Client\Model\PaymentContainersProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.containers
     *             [id] => PCT_3TWVACJZA2N87HBXN0ZAVD3Z53P7AZ
     *             [contract] => Secuconnect\Client\Model\PaymentContractsProductModel Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.contracts
     *                             [id] => PCR_M32SCZ98Q2N3U4GW70ZAVWWE47XPAH
     *                         )
     *                 )
     *             [customer] => Secuconnect\Client\Model\PaymentCustomersProductModel Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.customers
     *                             [id] => PCU_W7YUSPPA22N87F8E70ZAVB4S0YH5AW
     *                         )
     *                 )
     *             [type] => bank_account
     *             [public] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE37503240001000000524
     *                             [bic] => FTSBDEFAXXX
     *                             [owner] => John Doe
     *                             [bankname] => ABN AMRO Bank, Frankfurt Branch
     *                             [purpose] => 
     *                         )
     *                 )
     *             [private] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE37503240001000000524
     *                             [bic] => FTSBDEFAXXX
     *                             [owner] => John Doe
     *                             [bankname] => ABN AMRO Bank, Frankfurt Branch
     *                         )
     *                 )
     *             [created] => 2019-06-17T14:43:29+02:00
     *             [mandate] => Secuconnect\Client\Model\PaymentContainerMandate Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [sepa_mandate_id] => 477206
     *                             [type] => COR1
     *                             [identification] => PAM/A44BNICA44L04ESG6
     *                         )
     *                 )
     *         )
     * )
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
