<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new PaymentTransactionsApi();

    // List transactions with a "incoming_payment_date" and order them by this date descending
    $query = 'incoming_payment_date:*';
    $sort = 'incoming_payment_date:desc';

    $response = $api_instance->getAll(
        10, // for test limit the result
        null,
        null,
        $query,
        $sort
    );

    print_r($response);
    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\PaymentTransactionsList Object
     * (
     *     [container:protected] => Array
     *         (
     *             [count] => 312
     *             [data] => Array
     *                 (
     *                     [0] => Secuconnect\Client\Model\PaymentTransactionsProductModel Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [object] => payment.transactions
     *                                     [id] => PCI_...
     *                                     [merchant] => Secuconnect\Client\Model\GeneralMerchantsProductModel Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [object] => general.merchants
     *                                                     [id] => MRC_...
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
     *                                     [trans_id] => 1234561231
     *                                     [product_id] => 310
     *                                     [product] => Payment in advance
     *                                     [product_raw] => Crowdfunding Vorkasse
     *                                     [zahlungsmittel_id] => 91233
     *                                     [contract_id] => 123442
     *                                     [amount] => 50000
     *                                     [currency] => EUR
     *                                     [created] => 2019-01-11T23:05:07+01:00
     *                                     [updated] => 2019-02-22T13:52:30+01:00
     *                                     [status] => 17
     *                                     [status_text] => ausgezahlt
     *                                     [incoming_payment_date] => 2019-01-14T12:00:00+01:00
     *                                     [details] => Secuconnect\Client\Model\PaymentTransactionsProductModelDetails Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [amount] => 50000
     *                                                     [cleared] => cleared
     *                                                     [status] => 17
     *                                                     [status_text] => ausgezahlt
     *                                                     [status_simple] => 1
     *                                                 )
     *                                         )
     *                                     [customer] => Secuconnect\Client\Model\PaymentTransactionsProductModelCustomer Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [forename] => Max
     *                                                     [surname] => Muster
     *                                                 )
     *                                         )
     *                                     [payment_data] => DE00 XXXX XXXX 1234
     *                                     [transaction_hash] => basasdbsdfa3123333
     *                                 )
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
