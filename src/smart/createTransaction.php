<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;

try {
    Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secupay-ag.de/api/v2');
    Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secupay-ag.de/');

    Authenticator::authenticateByClientCredentials(...[
        '...',
        '...'
    ]);

    $transaction = new SmartTransactionsDTO();
    $transaction->setIsDemo(true);
    $transaction->setIntent("order");
    $transaction->setContract(new ProductInstanceID(['id' => 'GCR_3QCX2UMNSE87Y698A5B90GD5MZWHP7']));

    /*
     * Add basket
     */
    $prod1 = new SmartTransactionsBasketProduct();
    $prod1->setId(0);
    $prod1->setItemType("article");
    $prod1->setDesc("Position 1 Order something");
    $prod1->setQuantity(1);
    $prod1->setSum(999);
    $prod1->setPriceOne(999);
    $prod1->setTax(19);
    $prod1->setReferenceId("1001");

    $SmartTransactionsBasket = new SmartTransactionsBasket();
    $SmartTransactionsBasket->setProducts([$prod1]);
    $transaction->setBasket($SmartTransactionsBasket);

    $SmartTransactionsBasketInfo = new SmartTransactionsBasketInfo();
    $SmartTransactionsBasketInfo->setSum(999);
    $SmartTransactionsBasketInfo->setCurrency("EUR");

    $transaction->setBasketInfo($SmartTransactionsBasketInfo);

    $api_instance = new SmartTransactionsApi();
    $response = $api_instance->addTransaction($transaction);

//    print_r($response);

    echo 'Your STX-ID:' . $response->getId() . chr(10);
    if (!empty($response->getCheckoutLinks())) {
        echo 'Your Smart-Checkout-Link: ' . $response->getCheckoutLinks()->getUrlCheckout() . chr(10);
    }


} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
