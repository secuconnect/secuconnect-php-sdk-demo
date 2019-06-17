<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentCustomersApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentCustomersDTO;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $customerContact = new Contact();
    $customerContact->setSalutation('Mr.');
    $customerContact->setTitle('Dr.');
    $customerContact->setForename('John');
    $customerContact->setSurname('Doe');
    $customerContact->setCompanyname('Example Inc.');
    $customerContact->setGender('m');
    $customerContact->setDob('1901-02-03');
    $customerContact->setUrlWebsite('example.com');
    $customerContact->setBirthplace('AnotherExampleCity');
    $customerContact->setNationality('german');
    $customerContact->setEmail('example123@example.com');
    $customerContact->setPhone('0049-123-456789');

    $customerContactAddress = new Address();
    $customerContactAddress->setType('invoice');
    $customerContactAddress->setStreet('example street');
    $customerContactAddress->setStreetNumber('6a');
    $customerContactAddress->setPostalCode('01234');
    $customerContactAddress->setCity('Testcity');
    $customerContactAddress->setCountry('DE');
    $customerContact->setAddress($customerContactAddress);

    $customer = new PaymentCustomersDTO();
    $customer->setContact($customerContact);

    $api_instance = new PaymentCustomersApi();
    $response = $api_instance->paymentCustomersPost($customer);

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
