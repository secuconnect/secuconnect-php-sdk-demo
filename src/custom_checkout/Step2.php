<?php

namespace Secuconnect\Demo\custom_checkout;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContext;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Demo\Globals;

/**
 * Custom Checkout
 *
 * Step 2: Create the Smart Transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/Custom_Checkout.html">Custom Checkout</a>
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
                ->setIntent("sale")
                ->setContract((new ProductInstanceID())
                    ->setId("GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0")
                )
                ->setCustomer((new PaymentCustomersProductModel())
                    ->setContact((new Contact())
                        ->setSalutation("Mr.")
                        ->setForename("Max")
                        ->setSurname("Mustermann")
                        ->setAddress((new Address())
                            ->setStreet("Max-Muster-Str.")
                            ->setStreetNumber("25a")
                            ->setPostalCode("09555")
                            ->setCity("Musterstadt")
                            ->setCountry("DE")
                            ->setAdditionalAddressData("Whg. 202")
                        )
                        ->setEmail("mmustermann@example.net")
                        ->setMobile("+49 177 5555555")
                        ->setPhone("+49 555 5555555")
                    )
                )
                ->setTransactionRef("Hotelbuchung 23.10.2020 für Hrn. Muster, Musterfirma GmbH Musterstadt")
                ->setBasketInfo((new SmartTransactionsBasketInfo())
                    ->setCurrency("EUR")
                    ->setSum(11970) // €119.70 (smallest currency unit)
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
             *             "salutation": "Mr.",
             *             "email": "mmustermann@example.net",
             *             "phone": "+495555555555",
             *             "mobile": "+491775555555",
             *             "address": {
             *                 "street": "Max-Muster-Str.",
             *                 "street_number": "25aa",
             *                 "city": "Musterstadt",
             *                 "postal_code": "09555",
             *                 "country": "DE",
             *                 "additional_address_data": "Whg. 202"
             *             }
             *         },
             *         "object": "payment.customers",
             *         "id": "PCU_3XDX9HQM62X4YJF535ZHR29T6SJ5AH"
             *     },
             *     "transactionRef": "Hotelbuchung 23.10.2020 f\u00fcr Hrn. Muster, Musterfirma GmbH Musterstadt",
             *     "basket_info": {
             *         "sum": 11970,
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
             *     "payment_context": {
             *         "auto_capture": true
             *     },
             *     ...
             *     "object": "smart.transactions",
             *     "id": "STX_3JWNJ0Q9W2X4YJF535ZHR29T6SJ5AJ"
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
