<?php

namespace Secuconnect\Demo\api_integration_of_smart_checkout;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionsApplicationContext;
use Secuconnect\Client\Model\SmartTransactionsApplicationContextReturnUrls;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Demo\Globals;

/**
 * API Integration of Smart Checkout
 *
 * Step 2: Create the Smart Transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/API_Integration_of_Smart_Checkout.html">API Integration of Smart Checkout</a>
 */
class Step2
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // fill basket
            $id = 0;
            $basketItems = [];
            $basketItems[] = (new SmartTransactionsBasketProduct())
                ->setId(++$id)
                ->setItemType("article")
                ->setDesc("ACME ball pen Modern Line 8050")
                ->setPriceOne(1595) // in euro-cent
                ->setTax(19)
                ->setQuantity(2);
            $basketItems[] = (new SmartTransactionsBasketProduct())
                ->setId(++$id)
                ->setItemType("article")
                ->setDesc("ACME pen case Modern Line")
                ->setPriceOne(1795) // in euro-cent
                ->setTax(19)
                ->setQuantity(1);

            // calculate the sum
            $sum = 0;
            foreach ($basketItems as $item) {
                if ("article" === $item->getItemType() || "shipping" === $item->getItemType()) {
                    $sum += $item->getPriceOne() * $item->getQuantity();
                }
                if ("coupon" === $item->getItemType()) {
                    $sum -= $item->getPriceOne() * $item->getQuantity();
                }
            }

            // init request
            $transaction = (new SmartTransactionsDTO())
                ->setIsDemo(true)
                ->setIntent("order")
                ->setContract((new ProductInstanceID())
                    ->setId("GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0")
                )
                ->setBasket((new SmartTransactionsBasket())
                    ->setProducts($basketItems)
                )
                ->setBasketInfo((new SmartTransactionsBasketInfo())
                    ->setSum($sum)
                    ->setCurrency("EUR")
                )
                ->setApplicationContext((new SmartTransactionsApplicationContext())
                    ->setReturnUrls((new SmartTransactionsApplicationContextReturnUrls())
                        ->setUrlSuccess("https://shop.example.com/PAYMENT-SUCCEEDED")
                        ->setUrlError("https://shop.example.com/PAYMENT-FAILED")
                        ->setUrlAbort("https://shop.example.com/PAYMENT-ABORTED")
                    )
                );

            // run api call
            $response = (new SmartTransactionsApi())->addTransaction($transaction);
            print_r($response->__toString());
            /*
             * Sample output:
             * ==============
             * {
             *     "created": "2021-04-28T12:00:00+02:00",
             *     "status": "created",
             *     "merchant": {
             *         "object": "general.merchants",
             *         "id": "MRC_WVHJQFQ4JNVYNG5B55TYK748ZCHQP8",
             *         "companyname": "Secupay Test-Shop"
             *     },
             *     "contract": {
             *         "object": "general.contracts",
             *         "id": "GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0"
             *     },
             *     "basket_info": {
             *         "sum": 4985,
             *         "currency": "EUR"
             *     },
             *     "basket": {
             *         "products": [
             *             {
             *                 "id": 1,
             *                 "item_type": "article",
             *                 "desc": "ACME ball pen Modern Line 8050",
             *                 "quantity": 2,
             *                 "priceOne": 1595,
             *                 "tax": 19
             *             },
             *             {
             *                 "id": 2,
             *                 "item_type": "article",
             *                 "desc": "ACME pen case Modern Line",
             *                 "quantity": 1,
             *                 "priceOne": 1795,
             *                 "tax": 19
             *             }
             *         ],
             *         "type": "default"
             *     },
             *     "is_demo": true,
             *     "checkout_links": {
             *         "url_checkout": "https://checkout-dev.secuconnect.com?..."
             *     },
             *     "intent": "order",
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_NPNF3464P2X4YJ5RABEHH3SGZJXWAH"
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
Step2::main();
