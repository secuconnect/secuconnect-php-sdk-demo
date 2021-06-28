<?php

namespace Secuconnect\Demo\custom_checkout_for_marketplace;

use Secuconnect\Client\Api\PaymentContainersApi;
use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\BankAccountDescriptor;
use Secuconnect\Client\Model\PaymentContainersDTO;
use Secuconnect\Client\Model\ProductInstanceUID;
use Secuconnect\Client\Model\SmartTransactionsPrepare;
use Secuconnect\Demo\Globals;

/**
 * Custom Checkout for Marketplace
 *
 * Step 3: Authorise and Capture for Direct Debit
 *
 * @see <a href="https://developer.secuconnect.com/integration/Custom_Checkout_for_Marketplace.html">Custom Checkout for Marketplace</a>
 */
class Step3
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // create a payment container
            $container = (new PaymentContainersApi())->paymentContainersPost(
                (new PaymentContainersDTO())
                    ->setType("bank_account")
                    ->setPrivate((new BankAccountDescriptor())
                        ->setOwner("Max Mustermann")
                        ->setIban("DE35500105175418493188")
                    )
            );

            // run api call
            $response = (new SmartTransactionsApi())->prepare(
                "STX_2SQHXHXRS2X4YJVP0NYSU5H73RWXA3",
                "debit",
                (new SmartTransactionsPrepare())
                    ->setContainer((new ProductInstanceUID())
                        ->setId($container->getId())
                    )
            );

            print_r($response->__toString());
            /*
             * Sample output:
             * ==============
             * {
             *     "created": "2021-04-28T12:00:00+02:00",
             *     "updated": "2021-04-28T12:05:00+02:00",
             *     "status": "ok",
             *     "container": {
             *         "type": "bank_account",
             *         "object": "payment.containers",
             *         "id": "PCT_39F8STS792X4YK6XESBEKWUP02PCAZ"
             *     },
             *     "trans_id": 40015109,
             *     "payment_method": "debit",
             *     "transactions": [
             *         {
             *             "trans_id": 40015109,
             *             "transaction_hash": "uoprjkwqcodm4972794",
             *             "object": "payment.transactions",
             *             "id": "PCI_WXNHW7J0SHJ000YDJ0HV5M5002K8N9"
             *         },
             *         {
             *             "trans_id": 40015110,
             *             "transaction_hash": "uoprjkwqcodm4972794_40015110",
             *             "reference_id": "1002",
             *             "object": "payment.transactions",
             *             "id": "PCI_7U6VCXFMPUPFH9Y8Y3C0AU5002K8NG"
             *         }
             *     ],
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_2SQHXHXRS2X4YJVP0NYSU5H73RWXA3"
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
