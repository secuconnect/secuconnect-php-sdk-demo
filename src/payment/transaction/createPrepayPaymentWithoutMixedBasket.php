<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentSecupayPrepaysApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\SecupayBasketItem;
use Secuconnect\Client\Model\SecupayRedirectUrl;
use Secuconnect\Client\Model\SecupayTransactionProductDTO;
use Secuconnect\Client\Model\SecupayTransactionProductDTOOptData;

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
    $transaction->setAccrual(true);

    $transaction->setRedirectUrl(new SecupayRedirectUrl());

    // See src/payment/customer/createCustomer.php for details
    $transaction->setCustomer(
        new PaymentCustomersProductModel(
            [
                'id' => 'PCU_3092RR0DB2NBEE6FN0ZAVFYEJZEYAW'
            ]
        )
    );

    $basketItem1 = new SecupayBasketItem();
    $basketItem1->setItemType('shipping');
    $basketItem1->setName('standard delivery');
    $basketItem1->setTax(19);
    $basketItem1->setTotal(1324);

    $basketItem2 = new SecupayBasketItem();
    $basketItem2->setItemType('article');
    $basketItem2->setArticleNumber(3211);
    $basketItem2->setQuantity(2);
    $basketItem2->setName('Fancy Item XYZ');
    $basketItem2->setEan(4123412341243);
    $basketItem2->setTax(19);
    $basketItem2->setTotal(2000);
    $basketItem2->setPrice(1000);

    $transaction->setBasket(
        [
            $basketItem1,
            $basketItem2
        ]
    );

    $api_instance = new PaymentSecupayPrepaysApi();
    $response = $api_instance->paymentSecupayprepaysPost($transaction);

    print_r($response);
    /*
     * Secuconnect\Client\Model\SecupayTransactionProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.secupayprepays
     *             [id] => igwibrzranbq3476703
     *             [trans_id] => 14249815
     *             [status] => authorized
     *             [amount] => 3324
     *             [currency] => EUR
     *             [basket] => Array
     *                 (
     *                     [0] => Secuconnect\Client\Model\SecupayBasketItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => shipping
     *                                     [name] => standard delivery
     *                                     [tax] => 19
     *                                     [total] => 1324
     *                                 )
     *                         )
     *                     [1] => Secuconnect\Client\Model\SecupayBasketItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [item_type] => article
     *                                     [article_number] => 3211
     *                                     [quantity] => 2
     *                                     [name] => Fancy Item XYZ
     *                                     [ean] => 4123412341243
     *                                     [tax] => 19
     *                                     [total] => 2000
     *                                     [price] => 1000
     *                                 )
     *                         )
     *                 )
     *             [transaction_status] => 25
     *             [accrual] => 1
     *             [payment_action] => sale
     *             [transfer_purpose] => TA 14249815
     *             [transfer_account] => Secuconnect\Client\Model\PaymentInformation Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE88300500000001747013
     *                             [bic] => WELADEDDXXX
     *                             [owner] => secupay AG
     *                             [bankname] => Landesbank Hessen-Thüringen Girozentrale NL. Düsseldorf
     *                         )
     *                 )
     *             [customer] => Secuconnect\Client\Model\PaymentCustomersProductModel Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.customers
     *                             [id] => PCU_3092RR0DB2NBEE6FN0ZAVFYEJZEYAW
     *                         )
     *                 )
     *             [redirect_url] => Secuconnect\Client\Model\SecupayRedirectUrl Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iframe_url] => https://api-testing.secupay-ag.de/payment/igwibrzranbq3476703
     *                             [url_success] => http://example.com
     *                             [url_failure] => http://example.com
     *                             [url_push] => https://example.com
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
