<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\SecupayPayoutDTO;
use Secuconnect\Client\Model\SecupayRedirectUrl;
use Secuconnect\Client\Model\SecupayTransactionListItem;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $transaction = new SecupayPayoutDTO();
    $transaction->setDemo(false);
    $transaction->setCurrency('EUR');
    $transaction->setContract('PCR_2MK6EM4NE2N72XCGN0ZAV5207TH9AY');

    $transaction->setRedirectUrl(new SecupayRedirectUrl());
    $transaction->getRedirectUrl()->setUrlPush('https://api.example.com/secuconnect/push');

    $transaction->setPurpose('Payout Test #1');
    $transaction->setOrderId('201900123');

    // See src/payment/createCustomer.php if you want to know how you can create a payment customer id
    $transaction->setCustomer('PCU_WK2DUNC8U2N72XBV70ZAV5207TH9AH');

    $listItem1 = new SecupayTransactionListItem();
    $listItem1->setReferenceId('2000.1');
    $listItem1->setName('Payout Purpose 1');
    $listItem1->setTransactionHash('hppbfplzdkzy3472363');
    $listItem1->setTotal(100); // in euro-cent

    $listItem2 = new SecupayTransactionListItem();
    $listItem2->setReferenceId('2000.2');
    $listItem2->setName('Payout Purpose 2');
    $listItem2->setContainerId('PCT_2PAYNDWCE2N72XC8N0ZAV5207TH9AK');
    $listItem2->setTotal(200); // in euro-cent

    $listItem3 = new SecupayTransactionListItem();
    $listItem3->setReferenceId('2000.3');
    $listItem3->setName('Payout Purpose 3');
    $listItem3->setTransactionId('PCI_DSVJBYCJG9X0GBMV8JCXMH4A28KKN8');
    $listItem3->setTotal(50); // in euro-cent

    $transaction->setTransactionList([
        $listItem1,
        $listItem2,
        $listItem3
    ]);

    // calculate the amount
    $amount = 0;
    foreach ($transaction->getTransactionList() as $item) {
        $amount += (int)$item->getTotal();
    }
    $transaction->setAmount($amount); // in euro-cent

    $api_instance = new \Secuconnect\Client\Api\PaymentSecupayPayoutApi();
    $response = $api_instance->paymentSecupaypayoutPost($transaction);

    print_r($response);

    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\SecupayPayoutProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.secupaypayout
     *             [id] => vvvjulhaacbp3472419
     *             [trans_id] => 14245347
     *             [status] => authorized
     *             [amount] => 350
     *             [currency] => EUR
     *             [purpose] => Payout Test #1
     *             [order_id] => 201900123
     *             [transaction_status] => 25
     *             [transaction_list] => Array
     *                 (
     *                     [0] => Secuconnect\Client\Model\SecupayTransactionListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => transaction_payout
     *                                     [reference_id] => 2000.1
     *                                     [name] => Payout Purpose 1
     *                                     [transaction_hash] => hppbfplzdkzy3472363
     *                                     [total] => 100
     *                                 )
     *                         )
     *                     [1] => Secuconnect\Client\Model\SecupayTransactionListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => transaction_payout
     *                                     [reference_id] => 2000.2
     *                                     [name] => Payout Purpose 2
     *                                     [container_id] => PCT_2PAYNDWCE2N72XC8N0ZAV5207TH9AK
     *                                     [total] => 200
     *                                 )
     *                         )
     *                     [2] => Secuconnect\Client\Model\SecupayTransactionListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => transaction_payout
     *                                     [reference_id] => 2000.2
     *                                     [name] => Payout Purpose 2
     *                                     [transaction_id] => PCI_DSVJBYCJG9X0GBMV8JCXMH4A28KKN8
     *                                     [total] => 50
     *                                 )
     *                         )
     *                 )
     *             [transfer_purpose] => TA 14245347
     *             [transfer_account] => Secuconnect\Client\Model\PaymentInformation Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE88300500000001747013
     *                             [bic] => WELADEDDXXX
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

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
