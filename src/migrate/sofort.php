<?php
/** @noinspection PhpUnhandledExceptionInspection */

require __DIR__ . '/init.php';

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContext;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionPaymentCustomerDTO;
use Secuconnect\Client\Model\SmartTransactionsApplicationContext;
use Secuconnect\Client\Model\SmartTransactionsApplicationContextReturnUrls;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Client\Model\SmartTransactionsPrepare;

try {
    $contact = new Contact();
    $contact->setSalutation('Mr.');
    $contact->setTitle('Dr.');
    $contact->setForename('John');
    $contact->setSurname('Doe');
    $contact->setCompanyname('Testfirma');
    $contact->setDob('1971-02-03');
    $contact->setBirthplace('MyBirthplace');
    $contact->setNationality('DE');
    // specifying email for customer is important, so the customer can receive Mandate information
    $contact->setEmail('example@example.com');
    $contact->setPhone('+49123456789');

    $address = new Address();
    $address->setStreet('Example Street');
    $address->setStreetNumber('6a');
    $address->setCity('ExampleCity');
    $address->setCountry('DE');
    $address->setPostalCode('01234');

    $contact->setAddress($address);

    $customer = new SmartTransactionPaymentCustomerDTO();
    $customer->setContact($contact);

    $sofort = new SmartTransactionsDTO();
    $sofort->setIsDemo(true);
    $sofort->setContract(new ProductInstanceID(['id' => 'GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0']));
    $basket_info = new SmartTransactionsBasketInfo();
    $basket_info->setSum(100); // Amount in cents (or in the smallest unit of the given currency)
    $basket_info->setCurrency('EUR'); // The ISO-4217 code of the currency
    $sofort->setBasketInfo($basket_info);
    $sofort->setTransactionRef('Your purpose from TestShopName');
    $sofort->setMerchantRef('201600123'); // The shop order id
    $sofort->setCustomer($customer);
    $application_context = new SmartTransactionsApplicationContext();
    $application_context->setCheckoutTemplate('COT_WD0DE66HN2XWJHW8JM88003YG0NEA2');
    // The customer will be redirected to "url_success" after you (the shop) has shown him the iframe,
    // and he has filled out the form in this iframe.
    // The url of this iframe will be returned in the response of this save request in the variable called "iframe_url".
    $return_urls = new SmartTransactionsApplicationContextReturnUrls();
    $return_urls->setUrlSuccess('http://shop.example.com/success.php');
    // The customer will be redirected to "url_failure" if we don't accept him for credit card payments.
    // You should offer him to pay with other payment methods on this page.
    $return_urls->setUrlError('http://shop.example.com/failure.php');
    $application_context->setReturnUrls($return_urls);
    $sofort->setApplicationContext($application_context);
    $payment_context = new PaymentContext();
    $payment_context->setAutoCapture(true);
//    $payment_context->setAccrual(true);
    $sofort->setPaymentContext($payment_context);

    $sofort = (new SmartTransactionsApi())->addTransaction($sofort);

    if ($sofort->getId()) {
        echo 'Created secupay sofort transaction with id: ' . $sofort->getId() . "\n";
        echo 'Sofort data: ' . print_r($sofort->__toString(), true) . "\n";
        echo 'Checkout-Link: ' . $sofort->getPaymentLinks()['sofort'] . "\n";
    } else {
        echo 'Sofort creation failed' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Created secupay sofort transaction with id: STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK
     * Sofort data: {
     *     ...
     *     "status": "created",
     *     "payment_links": {
     *         "sofort": "https:\/\/pay-dev.secuconnect.com?payment-method=sofort&stx=STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK&contract=GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0&server=testing",
     *         "general": "https:\/\/pay-dev.secuconnect.com?stx=STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK&contract=GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0&server=testing"
     *     },
     *     "id": "STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK"
     * }
     * Checkout-Link: https://pay-dev.secuconnect.com?payment-method=sofort&stx=STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK&contract=GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0&server=testing
     */

    // add payment container
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}
