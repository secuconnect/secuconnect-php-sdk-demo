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
                'id' => 'PCU_3R3SSQEF22N6UGGZ70ZAV938Z8UKAW'
            ]
        )
    );

    $subTransactionForSubContract = new SecupayBasketItem();
    $subTransactionForSubContract->setItemType('sub_transaction');
    $subTransactionForSubContract->setName('Order 123');
    $subTransactionForSubContract->setContractId('PCR_XJH365T7S2N630M7T3H58CF8HF9AAH'); // See src/payment/contract/create* for details
    $subTransactionForSubContract->setTotal(3324);

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
    $basketItem2->setEan("4123412341243");
    $basketItem2->setTax(19);
    $basketItem2->setTotal(2000);
    $basketItem2->setPrice(1000);

    $basketItem3 = new SecupayBasketItem();
    $basketItem3->setItemType('stakeholder_payment');
    $basketItem3->setContractId('PCR_HS08HCV0V2N630M7T3H58CF9ZE2YAW'); // This id is fixed for the platform
    $basketItem3->setName('Platform Provision');
    $basketItem3->setTotal(300);

    $subTransactionForSubContract->setSubBasket(
        [
            $basketItem1,
            $basketItem2,
            $basketItem3
        ]
    );

    $transaction->setBasket(
        [
            $subTransactionForSubContract
        ]
    );

    $api_instance = new PaymentSecupayPrepaysApi();
    $response = $api_instance->paymentSecupayprepaysPost($transaction);

    print_r($response);
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
