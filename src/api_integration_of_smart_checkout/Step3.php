<?php

namespace Secuconnect\Demo\api_integration_of_smart_checkout;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Demo\Globals;

/**
 * API Integration of Smart Checkout
 *
 * Step 3: Get the details of a completed smart transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/API_Integration_of_Smart_Checkout.html">API Integration of Smart Checkout</a>
 */
class Step3
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // run api call
            $response = (new SmartTransactionsApi())->getOne("STX_NPNF3464P2X4YJ5RABEHH3SGZJXWAH");
            print_r($response->__toString());
            /*
             * Sample output:
             * ==============
             * {
             *     "created": "2021-04-28T12:00:00+02:00",
             *     "updated": "2021-04-28T12:05:00+02:00",
             *     "status": "received",
             *     "customer": {
             *         "contact": {
             *             "forename": "Max",
             *             "surname": "Muster",
             *             "email": "sd@example.com",
             *             "phone": "+4912342134123",
             *             "address": {
             *                 "street": "Kolumbastr.",
             *                 "street_number": "3TEST",
             *                 "city": "K\u00f6ln",
             *                 "postal_code": "50667",
             *                 "country": "DE"
             *             }
             *         },
             *         "object": "payment.customers",
             *         "id": "PCU_WMZDQQSRF2X4YJ67G997YE8Y26XSAW"
             *     },
             *     "container": {
             *         "type": "bank_account",
             *         "object": "payment.containers",
             *         "id": "PCT_WCB4H23TW2X4YJ6Y8B7FD5N5MS8NA2"
             *     },
             *     "trans_id": 40015106,
             *     "payment_method": "debit",
             *     "transactions": [
             *         {
             *             "trans_id": 40015106,
             *             "transaction_hash": "qkglowlxxbyz4972791",
             *             "object": "payment.transactions",
             *             "id": "PCI_WP7AEW23T2SMTJ0MCJTTXQ5002K8N6"
             *         }
             *     ],
             *     "is_demo": true,
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_NPNF3464P2X4YJ5RABEHH3SGZJXWAH"
             * }
             */
        } catch (ApiException $e) {
            echo $e->getTraceAsString();

            // show the error message from the api
            var_dump($e->getResponseBody());

            $supportId = '';
            if (isset($e->getResponseBody()->supportId)) {
                $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
            }

            echo 'Request was not successful, check the log for details.' . $supportId;
        }
    }
}

require_once __DIR__ . '/../../vendor/autoload.php';
Step3::main();
