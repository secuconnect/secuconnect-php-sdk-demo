<?php

namespace Secuconnect\Demo\payment_with_smart_checkout;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Demo\Globals;

/**
 * Payment with Smart Checkout
 *
 * Step 3: Get the details of a completed smart transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/Payment_with_Smart_Checkout.html">Payment with Smart Checkout</a>
 */
class Step3
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // run api call
            $response = (new SmartTransactionsApi())->getOne("STX_WPATWC3RJ2X4YKCZYU2NJCTMMC6YAJ");
            print_r($response->__toString());
            /*
             * Sample output:
             * ==============
             * {
             *     "created": "2021-04-28T12:00:00+02:00",
             *     "updated": "2021-04-28T12:05:00+02:00",
             *     "status": "ok",
             *     "container": {
             *         "type": "credit_card",
             *         "object": "payment.containers",
             *         "id": "PCT_2FNN7XCTY2X4YKFPD4WCD6EZ4CFWAZ"
             *     },
             *     "trans_id": 40015111,
             *     "payment_method": "creditcard",
             *     "transactions": [
             *         {
             *             "trans_id": 40015111,
             *             "transaction_hash": "tqussdueuatn4972796",
             *             "object": "payment.transactions",
             *             "id": "PCI_WGWAEV0R6GUNST2TCFG6GZ5002K8NH"
             *         }
             *     ],
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_WPATWC3RJ2X4YKCZYU2NJCTMMC6YAJ"
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
