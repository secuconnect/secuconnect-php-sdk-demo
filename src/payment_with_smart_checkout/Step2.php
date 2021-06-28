<?php

namespace Secuconnect\Demo\payment_with_smart_checkout;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContext;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionsApplicationContext;
use Secuconnect\Client\Model\SmartTransactionsApplicationContextReturnUrls;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Demo\Globals;

/**
 * Payment with Smart Checkout
 *
 * Step 2: Create the Smart Transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/Payment_with_Smart_Checkout.html">Payment with Smart Checkout</a>
 */
class Step2
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // init request
            $transaction = (new SmartTransactionsDTO())
                ->setIsDemo(true)
                ->setContract((new ProductInstanceID())
                    ->setId("GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0")
                )
                ->setCustomer((new PaymentCustomersProductModel())
                    ->setContact((new Contact())
                        ->setForename("Max")
                        ->setSurname("Mustermann")
                        ->setEmail("max@example.net")
                    )
                )
                ->setIntent("sale")
                ->setBasketInfo((new SmartTransactionsBasketInfo())
                    ->setSum(500)
                    ->setCurrency("EUR")
                )
                ->setApplicationContext((new SmartTransactionsApplicationContext())
                    ->setReturnUrls((new SmartTransactionsApplicationContextReturnUrls())
                        ->setUrlSuccess("https://shop.example.com/payment-success")
                        ->setUrlError("https://shop.example.com/payment-failure")
                        ->setUrlAbort("https://shop.example.com/payment-abort")
                    )
                )
                ->setPaymentContext((new PaymentContext())
                    ->setAutoCapture(true)
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
             *     "customer": {
             *         "contact": {
             *             "forename": "Max",
             *             "surname": "Mustermann",
             *             "email": "max@example.net"
             *         },
             *         "object": "payment.customers",
             *         "id": "PCU_TYYMMJW5K2X4YKCZYU2NJCTMMC6YAH"
             *     },
             *     "basket_info": {
             *         "sum": 500,
             *         "currency": "EUR"
             *     },
             *     "is_demo": true,
             *     "intent": "sale",
             *     "payment_links": {
             *         "prepaid": "https://checkout-dev.secuconnect.com?...",
             *         "debit": "https://checkout-dev.secuconnect.com?...",
             *         "creditcard": "https://checkout-dev.secuconnect.com?...",
             *         "invoice": "https://checkout-dev.secuconnect.com?...",
             *         "sofort": "https://checkout-dev.secuconnect.com?...",
             *         "general": "https://checkout-dev.secuconnect.com?..."
             *     },
             *     "application_context": {
             *         "return_urls": {
             *             "url_success": "https://shop.example.com/payment-success",
             *             "url_abort": "https://shop.example.com/payment-abort",
             *             "url_error": "https://shop.example.com/payment-failure"
             *         }
             *     },
             *     "payment_context": {
             *         "auto_capture": true
             *     },
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_WPATWC3RJ2X4YKCZYU2NJCTMMC6YAJ"
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
