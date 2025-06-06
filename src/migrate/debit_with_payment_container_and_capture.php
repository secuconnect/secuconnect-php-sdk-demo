<?php
/** @noinspection PhpUnhandledExceptionInspection */

require __DIR__ . '/init.php';

use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\BankAccountDescriptor;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContext;
use Secuconnect\Client\Model\ProductInstanceID;
use Secuconnect\Client\Model\SmartTransactionPaymentContainerDTO;
use Secuconnect\Client\Model\SmartTransactionPaymentCustomerDTO;
use Secuconnect\Client\Model\SmartTransactionsApplicationContext;
use Secuconnect\Client\Model\SmartTransactionsApplicationContextReturnUrls;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Client\Model\SmartTransactionsPrepare;
use Secuconnect\Client\Model\SmartTransactionsStart;

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

    $debit = new SmartTransactionsDTO();
    $debit->setIsDemo(true);
    $debit->setContract(new ProductInstanceID(['id' => 'GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0']));
    $basket_info = new SmartTransactionsBasketInfo();
    $basket_info->setSum(100); // Amount in cents (or in the smallest unit of the given currency)
    $basket_info->setCurrency('EUR'); // The ISO-4217 code of the currency
    $debit->setBasketInfo($basket_info);
    $debit->setTransactionRef('Your purpose from TestShopName');
    $debit->setMerchantRef('201600123'); // The shop order id
    $debit->setCustomer($customer);
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
    $debit->setApplicationContext($application_context);
    $payment_context = new PaymentContext();
    $payment_context->setAutoCapture(false);
    $payment_context->setAccrual(true);
    $debit->setPaymentContext($payment_context);

    $debit = (new SmartTransactionsApi())->addTransaction($debit);

    if ($debit->getId()) {
        echo 'Created secupay debit transaction with id: ' . $debit->getId() . "\n";
        echo 'Debit data: ' . print_r($debit->__toString(), true) . "\n";
    } else {
        echo 'Debit creation failed' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Created secupay debit transaction with id: STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK
     * Debit data: {
     *     ...
     *     "status": "created",
     *     "id": "STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK"
     * }
     */

    // add payment container
    $prepare_data = new SmartTransactionsPrepare();
    $container = new SmartTransactionPaymentContainerDTO();
    $container->setType('bank_account');
    $bank_account = new BankAccountDescriptor();
    $bank_account->setOwner('Max Mustermann');
    $bank_account->setIban('DE37503240001000000524');
    $container->setPrivate($bank_account);
    $prepare_data->setContainer($container);

    $debit = (new SmartTransactionsApi())->prepare($debit->getId(), 'debit', $prepare_data);

    if ($debit->getId() && $debit->getStatus() === 'approved') {
        echo 'Prepared (reserved) secupay debit transaction with id: ' . $debit->getId() . "\n";
        echo 'Debit data: ' . print_r($debit->__toString(), true) . "\n";
    } else {
        echo 'Debit prepare failed' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Prepared (reserved) secupay debit transaction with id: STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK
     * Debit data: {
     *     ...
     *     "status": "approved",
     *     "id": "STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK"
     * }
     */

    // capture
    $start_data = new SmartTransactionsStart();
    $debit = (new SmartTransactionsApi())->startTransaction($debit->getId(), 'debit', $start_data);

    if ($debit->getId() && $debit->getStatus() === 'ok') {
        echo 'Completed secupay debit transaction with id: ' . $debit->getId() . "\n";
        echo 'Debit data: ' . print_r($debit->__toString(), true) . "\n";
    } else {
        echo 'Debit capture failed' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Completed secupay debit transaction with id: STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK
     * Debit data: {
     *     ...
     *     "status": "approved",
     *     "id": "STX_WC3HTTY372PAYSVPVCN9ZM5R0YM9AK"
     * }
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
