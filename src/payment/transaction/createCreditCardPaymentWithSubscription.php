<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentSecupayCreditcardsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\SecupayRedirectUrl;
use Secuconnect\Client\Model\SecupayTransactionProductDTO;
use Secuconnect\Client\Model\SecupayTransactionProductDTOOptData;
use Secuconnect\Client\Model\SecupayTransactionProductDTOSubscription;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $transaction = new SecupayTransactionProductDTO();
    $transaction->setOptData(new SecupayTransactionProductDTOOptData());
    $transaction->getOptData()->setLanguage('de_DE'); // or 'en_US'

    $transaction->setAmount(3324); // in euro-cent
    $transaction->setCurrency('EUR');
    $transaction->setDemo(true);

    $transaction->setRedirectUrl(new SecupayRedirectUrl());

    // See src/payment/customer/createCustomer.php for details
    $transaction->setCustomer(
        new PaymentCustomersProductModel(
            [
                'id' => 'PCU_3J2ZD5H8S2N4BCYCN0ZAV3W80X4YAH'
            ]
        )
    );

    // Activate the option to reuse the payment transaction (subscription / recurring payment)
    $transaction->setSubscription(
        new SecupayTransactionProductDTOSubscription(
            [
                'purpose' => 'Payment for www.example.com'
            ]
        )
    );

    $api_instance = new PaymentSecupayCreditcardsApi();
    $response = $api_instance->paymentSecupaycreditcardsPost($transaction);

    print_r($response);

    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\SecupayTransactionProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.secupaycreditcards
     *             [id] => csdsnhtbvgco3478376
     *             [trans_id] => 14251519
     *             [status] => internal_server_status
     *             [amount] => 3324
     *             [currency] => EUR
     *             [transaction_status] => 1
     *             [accrual] => 1
     *             [payment_action] => sale
     *             [customer] => Secuconnect\Client\Model\PaymentCustomersProductModel Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.customers
     *                             [id] => PCU_3J2ZD5H8S2N4BCYCN0ZAV3W80X4YAH
     *                             [contract] => Secuconnect\Client\Model\ProductInstanceUID Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [object] => payment.contracts
     *                                             [id] => PCR_M32SCZ98Q2N3U4GW70ZAVWWE47XPAH
     *                                         )
     *                                 )
     *                             [contact] => Secuconnect\Client\Model\Contact Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [forename] => John
     *                                             [surname] => Doe
     *                                             [companyname] => Example Inc.
     *                                         )
     *                                 )
     *                             [created] => 2019-03-15T09:42:01+01:00
     *                         )
     *                 )
     *             [redirect_url] => Secuconnect\Client\Model\SecupayRedirectUrl Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iframe_url] => https://api-testing.secupay-ag.de/payment/csdsnhtbvgco3478376
     *                             [url_success] => http://example.com
     *                             [url_failure] => http://example.com
     *                             [url_push] => https://example.com
     *                         )
     *                 )
     *             [subscription] => Secuconnect\Client\Model\SecupayTransactionProductDTOSubscription Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [purpose] => Payment for www.example.com
     *                             [id] => 1300
     *                         )
     *                 )
     *         )
     * )
     */

    // Store the Subscription-ID
    if ($response->getId()) {
        /*
         * After creating the first payment a so-called Subscription-ID will be returned in the response object.
         * This have to be stored and can then be reused for each recurring payments.
         */
        $subscriptionId = $response->getSubscription()->getId();
        echo 'Subscription-ID: ' . $subscriptionId . PHP_EOL;

        // Open the payment iframe
        echo 'The payer needs to open this URL: ' . $response->getRedirectUrl()->getIframeUrl() . PHP_EOL;
        /*
         * Sample output:
         * ==============
         * Subscription-ID: 1299
         * The payer needs to open this URL: https://api-testing.secupay-ag.de/payment/zsxcztrtutqj3478375
         */
    }

    unset($api_instance);
    unset($transaction);
    unset($response);

    // Reuse subscription
    /*
     * As soon as a customer has successfully completed a payment,
     * it's possible to reuse the payment without the customer needs to re-enter the payment data again.
     */
    $transaction2 = new SecupayTransactionProductDTO();
    $transaction2->setAmount(3324); // in euro-cent
    $transaction2->setCurrency('EUR');

    // Add the customer (id) which you have created before
    $transaction2->setCustomer(
        new PaymentCustomersProductModel(
            [
                'id' => 'PCU_3J2ZD5H8S2N4BCYCN0ZAV3W80X4YAH'
            ]
        )
    );

    // Optional: Define the contract (to which merchant belongs the payment transaction) using the ID:
//    $transaction2->setContract('PCR_3AYQR6T272M83WTYX75XU8CZNM8UA7');

    // Activate the option to reuse the payment transaction
    $transaction2->setSubscription(
        new SecupayTransactionProductDTOSubscription(
            [
                'id' => 1299
            ]
        )
    );
    $api_instance2 = new PaymentSecupayCreditcardsApi();
    $response2 = $api_instance2->paymentSecupaycreditcardsPost($transaction2);

    print_r($response2);
    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\SecupayTransactionProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.secupaycreditcards
     *             [id] => urevwqkkihvm3478378
     *             [trans_id] => 14251521
     *             [status] => accepted
     *             [amount] => 3324
     *             [currency] => EUR
     *             [purpose] => test
     *             [transaction_status] => 11
     *             [payment_action] => sale
     *             [customer] => Secuconnect\Client\Model\PaymentCustomersProductModel Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.customers
     *                             [id] => PCU_3J2ZD5H8S2N4BCYCN0ZAV3W80X4YAH
     *                             [contract] => Secuconnect\Client\Model\ProductInstanceUID Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [object] => payment.contracts
     *                                             [id] => PCR_M32SCZ98Q2N3U4GW70ZAVWWE47XPAH
     *                                         )
     *                                 )
     *                             [contact] => Secuconnect\Client\Model\Contact Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [forename] => John
     *                                             [surname] => Doe
     *                                             [companyname] => Example Inc.
     *                                         )
     *                                 )
     *                             [created] => 2019-03-15T09:42:01+01:00
     *                         )
     *                 )
     *             [used_payment_instrument] => Secuconnect\Client\Model\SecupayTransactionProductModelUsedPaymentInstrument Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [type] => credit_card
     *                             [data] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [pan] => 4929 XXXX XXXX 3635
     *                                         )
     *                                 )
     *                         )
     *                 )
     *             [redirect_url] => Secuconnect\Client\Model\SecupayRedirectUrl Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iframe_url] => https://api-testing.secupay-ag.de/payment/urevwqkkihvm3478378
     *                         )
     *                 )
     *             [subscription] => Secuconnect\Client\Model\SecupayTransactionProductDTOSubscription Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [id] => 1299
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
