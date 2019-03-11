<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../vendor/autoload.php';

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Client\Model\SmartTransactionsSubBasketProduct;

try {

    /*
     * The next will print for the first time:
     *
     * Your user code is: cryrs405
     * Your verification url is: https://www.secuoffice.com/
     */
    Authenticator::authenticateByDevice(...[
        '...',
        '...',
        '/vendor/.../uuid/...'
    ]);


//    $api_instance = new \Secuconnect\Client\Api\GeneralCo;


    $api_instance = new \Secuconnect\Client\Api\GeneralMerchantsApi();
    var_dump('Access-Token: ' . $api_instance->getApiClient()->getConfig()->getAccessToken());
    var_dump($api_instance->getAll(1));


    $subBasket1 = new SmartTransactionsSubBasketProduct();
    $subBasket1->setId(1);
    $subBasket1->setItemType('article');
    $subBasket1->setQuantity(6);
    $subBasket1->setDesc("Coca Cola 0,33l Dose");
    $subBasket1->setEan("12938462834689234");
    $subBasket1->setPriceOne(10);
    $subBasket1->setReferenceId("1001.1");
    $subBasket1->setTax(0);

    $prod1 = new SmartTransactionsBasketProduct();
    $prod1->setId(1);
    $prod1->setItemType('sub_transaction');
    $prod1->setReferenceId("1000");
    $prod1->setDesc("Order #1");
    $prod1->setContractId("GCR_...");
    $prod1->setSubBasket(array($subBasket1));
    $prod1->setQuantity(1);
    $prod1->setPriceOne(60);
    $prod1->setSum(60);
    $prod1->setTax(0);

    $SmartTransactionsBasket = new SmartTransactionsBasket();
    $SmartTransactionsBasket->setProducts(array($prod1));


    $SmartTransactionsDTO = new SmartTransactionsDTO();
    $SmartTransactionsDTO->setBasket($SmartTransactionsBasket);


    $SmartTransactionsBasketInfo = new SmartTransactionsBasketInfo();
    $SmartTransactionsBasketInfo->setSum(60);

    $SmartTransactionsDTO->setBasketInfo($SmartTransactionsBasketInfo);

    $api = new SmartTransactionsApi();
    $createdTransaction = $api->addTransaction($SmartTransactionsDTO);

    print_r($createdTransaction);

    $api->startTransaction($createdTransaction->getId(), 'cashless');

} catch (ApiException $e) {
    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
