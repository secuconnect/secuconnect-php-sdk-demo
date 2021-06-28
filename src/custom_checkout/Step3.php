<?php

namespace Secuconnect\Demo\custom_checkout;

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
 * Custom Checkout
 *
 * Step 3: Authorize and Capture for Direct Debit
 *
 * @see <a href="https://developer.secuconnect.com/integration/Custom_Checkout.html">Custom Checkout</a>
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
                "STX_3JWNJ0Q9W2X4YJF535ZHR29T6SJ5AJ",
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
             *         "id": "PCT_NAW3Z3FHS2X4YJFR0NHWB3AM2YY8A2"
             *     },
             *     "trans_id": 40015108,
             *     "payment_method": "debit",
             *     "transactions": [
             *         {
             *             "trans_id": 40015108,
             *             "transaction_hash": "mwekikpdsswi4972793",
             *             "object": "payment.transactions",
             *             "id": "PCI_WVC27UT5T8UA46Z2YTAGFB5002K8N8"
             *         }
             *     ],
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_3JWNJ0Q9W2X4YJF535ZHR29T6SJ5AJ"
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
