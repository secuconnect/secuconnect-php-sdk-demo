<?php

namespace Secuconnect\Demo;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentSecupayPayoutApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\SecupayPayoutDTO;
use Secuconnect\Client\Model\SecupayRedirectUrl;
use Secuconnect\Client\Model\SecupayTransactionListItem;

try {
    Authenticator::authenticateByClientCredentials(
        ...Globals::OAuthClientCredentials
    );

    $transaction = new SecupayPayoutDTO();
    $transaction->setCurrency('EUR');
    $transaction->setContract('GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0');

    $transaction->setRedirectUrl(new SecupayRedirectUrl());
    $transaction->getRedirectUrl()->setUrlPush('https://api.example.com/secuconnect/push');

    $transaction->setPurpose('Payout Test #1');
    $transaction->setOrderId('201900123');

    // See src/payment/createCustomer.php if you want to know how you can create a payment customer id
    $transaction->setCustomer(new PaymentCustomersProductModel(['id' => 'PCU_W8X56E8Q52PCRUUAHVFVJ9970CMPAJ']));

    $listItem1 = new SecupayTransactionListItem();
    $listItem1->setReferenceId('2000.1');
    $listItem1->setName('Payout via transaction_hash');
    $listItem1->setTransactionHash('fepqkzqgunfb11102198');
    $listItem1->setTotal(100); // in euro-cent

    $listItem2 = new SecupayTransactionListItem();
    $listItem2->setReferenceId('2000.2');
    $listItem2->setName('Payout via payment.containers');
    $listItem2->setContainerId('PCT_8SU4C67WK2PB8HBJHF0838NHGZ30A4');
    $listItem2->setTotal(200); // in euro-cent

    $listItem3 = new SecupayTransactionListItem();
    $listItem3->setReferenceId('2000.3');
    $listItem3->setName('Payout via payment.transactions');
    $listItem3->setTransactionId('PCI_2U5DAY4JPVJRAAXV7FNQT248WA6AMG');
    $listItem3->setTotal(50); // in euro-cent

    $transaction->setTransactionList(
        [
            $listItem1,
            $listItem2,
            $listItem3,
        ]
    );

    // calculate the amount
    $amount = 0;
    foreach ($transaction->getTransactionList() as $item) {
        $amount += (int)$item->getTotal();
    }
    $transaction->setAmount($amount); // in euro-cent

    $api_instance = new PaymentSecupayPayoutApi();
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
     *             [id] => qqtcpqsspaph11102243
     *             [trans_id] => 110152037
     *             [payment_id] => PCI_5CZSUXGCBX9QUXZPDZHFC248WA8WMN
     *             [status] => pending
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
     *                                     [name] => Payout via transaction_hash
     *                                     [transaction_hash] => fepqkzqgunfb11102198
     *                                     [total] => 100
     *                                 )
     *                         )
     *                     [1] => Secuconnect\Client\Model\SecupayTransactionListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => transaction_payout
     *                                     [reference_id] => 2000.2
     *                                     [name] => Payout via payment.containers
     *                                     [container_id] => PCT_8SU4C67WK2PB8HBJHF0838NHGZ30A4
     *                                     [total] => 200
     *                                 )
     *                         )
     *                     [2] => Secuconnect\Client\Model\SecupayTransactionListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => transaction_payout
     *                                     [reference_id] => 2000.3
     *                                     [name] => Payout via payment.transactions
     *                                     [transaction_id] => PCI_2U5DAY4JPVJRAAXV7FNQT248WA6AMG
     *                                     [total] => 50
     *                                 )
     *                         )
     *                 )
     *             [transfer_purpose] => TA 110152037
     *             [transfer_account] => Secuconnect\Client\Model\PaymentInformation Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE81850400611005523759
     *                             [bic] => COBADEFFXXX
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
