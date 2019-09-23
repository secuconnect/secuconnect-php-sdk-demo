<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;

try {
    Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secupay-ag.de/api/v2');
    Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secupay-ag.de/');

    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new \Secuconnect\Client\Api\PaymentTransactionsApi();

    // FIRST STEP: get the "trans_id" of the parent transaction
    $payment_id = 'kfycfrskphjg3468286';
    $query = 'transaction_hash:' . $payment_id;

    $response = $api_instance->getAll(
        1,
        null,
        ['trans_id', 'id'],
        $query
    );
    print_r($response);
    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\PaymentTransactionsList Object
     * (
     *   [container:protected] => Array
     *     (
     *       [count] => 1
     *       [data] => Array
     *         (
     *           [0] => Secuconnect\Client\Model\PaymentTransactionsProductModel Object
     *           (
     *             [container:protected] => Array
     *               (
     *                 [id] => PCI_DDB2F7I0AYMHM762TVVBQ305853V3H
     *                 [trans_id] => 14228356
     *               )
     *           )
     *         )
     *     )
     * )
     */

    if ($response->getData()[0]['trans_id']) {
        $parent_trans_id = $response->getData()[0]['trans_id'];

        echo 'The TA-CODE (trans_id) of the parent is: ' . $parent_trans_id . PHP_EOL;
        echo 'You can call the details now by using this id: "' . $response->getData()[0]['id'] . '"' . PHP_EOL;
        /*
         * Sample output:
         * ==============
         * The TA-CODE (trans_id) of the parent is: 14228356
         * You can call the details now by using this id: "PCI_DDB2F7I0AYMHM762TVVBQ305853V3H"
         */

        // SECOND STEP: get all transactions which have this id as parent.
        $query2 = 'parents.trans_id:' . $parent_trans_id;

        $response2 = $api_instance->getAll(
            10,
            null,
            null,
            $query2
        );

        print_r($response2);
        /*
         * Sample output:
         * ==============
         * Secuconnect\Client\Model\PaymentTransactionsList Object
         * (
         *     [container:protected] => Array
         *         (
         *             [count] => 1
         *             [data] => Array
         *                 (
         *                     [0] => Secuconnect\Client\Model\PaymentTransactionsProductModel Object
         *                         (
         *                             [container:protected] => Array
         *                                 (
         *                                     [object] => payment.transactions
         *                                     [id] => PCI_6CSDCOGBA5TCWMWXKMBU3K3DLCA2VG
         *                                     [merchant] => Secuconnect\Client\Model\GeneralMerchantsProductModel Object
         *                                         (
         *                                             [container:protected] => Array
         *                                                 (
         *                                                     [object] => general.merchants
         *                                                     [id] => MRC_M7F8GUZNP318Z0YZQ9KWHZU24J1RRH
         *                                                     [type] => 11
         *                                                     [user] => Secuconnect\Client\Model\GeneralMerchantsUser Object
         *                                                         (
         *                                                             [container:protected] => Array
         *                                                                 (
         *                                                                     [name] => Maxi Muster
         *                                                                     [companyname] => Mustercompany
         *                                                                 )
         *                                                         )
         *                                                 )
         *                                         )
         *                                     [trans_id] => 14228357
         *                                     [product_id] => 36
         *                                     [product] => Payment in advance
         *                                     [product_raw] => Vorkasse
         *                                     [contract_id] => 340439
         *                                     [amount] => 1930
         *                                     [currency] => EUR
         *                                     [created] => 2019-03-03T20:16:29+01:00
         *                                     [updated] => 2019-03-03T20:16:29+01:00
         *                                     [status] => 25
         *                                     [status_text] => Vorkasse wartend
         *                                     [details] => Secuconnect\Client\Model\PaymentTransactionsProductModelDetails Object
         *                                         (
         *                                             [container:protected] => Array
         *                                                 (
         *                                                     [amount] => 1930
         *                                                     [cleared] => open
         *                                                     [status] => 25
         *                                                     [status_text] => Vorkasse wartend
         *                                                     [status_simple] => 2
         *                                                     [description] => Mustercompany - OrderNr #:100001004
         *                                                     [description_raw] => Mustercompany - OrderNr #:100001004
         *                                                 )
         *                                         )
         *                                     [customer] => Secuconnect\Client\Model\PaymentTransactionsProductModelCustomer Object
         *                                         (
         *                                             [container:protected] => Array
         *                                                 (
         *                                                     [companyname] => Mustercompany AG
         *                                                     [forename] => Max
         *                                                     [surname] => Muster
         *                                                 )
         *                                         )
         *                                     [transaction_hash] => kfycfrskphjg3468287
         *                                 )
         *                         )
         *                 )
         *         )
         * )
         */
    }

} catch (ApiException $e) {

    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
