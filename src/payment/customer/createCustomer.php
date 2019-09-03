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
    /*
     * Secuconnect\Client\Model\PaymentCustomersProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => payment.customers
     *             [id] => PCU_3092RR0DB2NBEE6FN0ZAVFYEJZEYAW
     *             [contract] => Secuconnect\Client\Model\ProductInstanceUID Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [object] => payment.contracts
     *                             [id] => PCR_...
     *                         )
     *                 )
     *             [contact] => Secuconnect\Client\Model\Contact Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [forename] => John
     *                             [surname] => Doe
     *                             [companyname] => Example Inc.
     *                             [salutation] => Mr.
     *                             [gender] => m
     *                             [title] => Dr.
     *                             [email] => example123@example.com
     *                             [phone] => 0049-123-456789
     *                             [dob] => 1901-02-03T00:00:00+01:00
     *                             [url_website] => example.com
     *                             [birthplace] => AnotherExampleCity
     *                             [nationality] => german
     *                             [address] => Secuconnect\Client\Model\Address Object
     *                                 (
     *                                     [container:protected] => Array
     *                                         (
     *                                             [street] => example street
     *                                             [street_number] => 6a
     *                                             [city] => Testcity
     *                                             [postal_code] => 01234
     *                                             [country] => DE
     *                                         )
     *                                 )
     *                         )
     *                 )
     *             [created] => 2019-09-03T15:58:55+02:00
     *         )
     * )
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
