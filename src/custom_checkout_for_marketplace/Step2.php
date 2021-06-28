<?php

namespace Secuconnect\Demo\custom_checkout_for_marketplace;

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContext;
use Secuconnect\Client\Model\PaymentCustomersProductModel;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Demo\Globals;

/**
 * Custom Checkout for Marketplace
 *
 * Step 2: Create the Smart Transaction
 *
 * @see <a href="https://developer.secuconnect.com/integration/Custom_Checkout_for_Marketplace.html">Custom Checkout for Marketplace</a>
 */
class Step2
{
    public static function main()
    {
        try {
            // init env
            Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));

            // fill sub-basket
            $id = 0;
            $subItems = [];
            $subItems[] = (new SmartTransactionsBasketProduct())
                ->setId(++$id)
                ->setItemType("article")
                ->setDesc("Coffee maker with remote control")
                ->setQuantity(1)
                ->setPriceOne(5000) // in euro-cent
                ->setTax(19);
            $subItems[] = (new SmartTransactionsBasketProduct())
                ->setId(++$id)
                ->setItemType("shipping")
                ->setDesc("Standard delivery 2-3 days")
                ->setQuantity(1)
                ->setPriceOne(350) // in euro-cent
                ->setTax(19);
            $subItems[] = (new SmartTransactionsBasketProduct())
                ->setId(++$id)
                ->setItemType("stakeholder_payment")
                ->setDesc("Marketplace fee")
                ->setSum(161) // in euro-cent
                ->setReferenceId("fee #12333")
                ->setContractId("GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0");

            // fill basket
            $basketItems = [];
            $basketItems[] = (new SmartTransactionsBasketProduct())
                ->setItemType("sub_transaction")
                ->setDesc("Orders for Muster-Elektrogeräte GmbH")
                ->setReferenceId("1002")
                ->setContractId("GCR_ZPMJGRH4SU3X0H3Y3WYB69XVXAG8PJ")
                ->setSubBasket($subItems);

            // calculate the sum
            $total = 0;
            foreach ($basketItems as $item) {
                $sum = 0;
                foreach ($item->getSubBasket() as $subItem) {
                    if ("article" === $subItem->getItemType() || "shipping" === $subItem->getItemType()) {
                        $sum += $subItem->getPriceOne() * $subItem->getQuantity();
                    }
                    if ("coupon" === $subItem->getItemType()) {
                        $sum -= $subItem->getPriceOne() * $subItem->getQuantity();
                    }
                }
                $total += $sum;
                $item->setSum($sum);
            }

            // init request
            $transaction = (new SmartTransactionsDTO())
                ->setIsDemo(true)
                ->setContract((new ProductInstanceID())
                    ->setId("GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0")
                )
                ->setCustomer((new PaymentCustomersProductModel())
                    ->setContact((new Contact())
                        ->setForename("Lesley")
                        ->setSurname("Mustermann")
                        ->setPhone("+49 555 5555555")
                        ->setMobile("+49 177 5555555")
                        ->setAddress((new Address())
                            ->setStreet("Musterstr.")
                            ->setStreetNumber("840")
                            ->setAdditionalAddressData("App. 506")
                            ->setPostalCode("09999")
                            ->setCity("East Palmaside")
                            ->setCountry("DE")
                        )
                        ->setEmail("Andrew37@example.org")
                        ->setDob("1965-12-31")
                    )
                )
                ->setIntent("sale")
                ->setBasket((new SmartTransactionsBasket())
                    ->setProducts($basketItems)
                )
                ->setBasketInfo((new SmartTransactionsBasketInfo())
                    ->setSum($total)
                    ->setCurrency("EUR")
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
             *             "forename": "Lesley",
             *             "surname": "Mustermann",
             *             "email": "Andrew37@example.org",
             *             "phone": "+495555555555",
             *             "mobile": "+491775555555",
             *             "dob": "1965-12-31T00:00:00+01:00",
             *             "address": {
             *                 "street": "Musterstr.",
             *                 "street_number": "840",
             *                 "city": "East Palmaside",
             *                 "postal_code": "09999",
             *                 "country": "DE",
             *                 "additional_address_data": "App. 506"
             *             }
             *         },
             *         "object": "payment.customers",
             *         "id": "PCU_H4RNC9W9J2X4YJVP0NYSU5H73RWXA2"
             *     },
             *     "basket_info": {
             *         "sum": 5350,
             *         "currency": "EUR"
             *     },
             *     "basket": {
             *         "products": [
             *             {
             *                 "item_type": "sub_transaction",
             *                 "desc": "Orders for Muster-Elektroger\u00e4te GmbH",
             *                 "sum": 5350,
             *                 "reference_id": "1002",
             *                 "contract_id": "GCR_ZPMJGRH4SU3X0H3Y3WYB69XVXAG8PJ",
             *                 "sub_basket": [
             *                     {
             *                         "id": 1,
             *                         "item_type": "article",
             *                         "desc": "Coffee maker with remote control",
             *                         "quantity": 1,
             *                         "priceOne": 5000,
             *                         "tax": 19
             *                     },
             *                     {
             *                         "id": 2,
             *                         "item_type": "shipping",
             *                         "desc": "Standard delivery 2-3 days",
             *                         "quantity": 1,
             *                         "priceOne": 350,
             *                         "tax": 19
             *                     },
             *                     {
             *                         "item_type": "stakeholder_payment",
             *                         "desc": "Marketplace fee",
             *                         "sum": 161,
             *                         "reference_id": "fee #12333",
             *                         "contract_id": "GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0"
             *                     }
             *                 ]
             *             }
             *         ],
             *         "type": "mixed"
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
             *     "id": "STX_2SQHXHXRS2X4YJVP0NYSU5H73RWXA3"
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
