<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentContractsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContractsDTORequestId;
use Secuconnect\Client\Model\PaymentInformation;

try {

    // Authenticate against the API
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new PaymentContractsApi();


    $contact = new Contact();
    /*
     * mandatory contact fields
     */
    $contact->setForename('John');
    $contact->setSurname('Doe'); // mandatory

    $address = new Address();
    $address->setType('invoice');
    $address->setStreet('example street');
    $address->setStreetNumber('6a');
    $address->setPostalCode('01234');
    $address->setCity('Testcity');
    $address->setCountry('DE');

    $contact->setAddress($address);

    /*
     * recommended contact fields
     */
    $contact->setCompanyname('Example Inc.'); // recommended
    $contact->setDob('1901-02-03'); // recommended
    $contact->setEmail('example@example.com'); // recommended
    $contact->setPhone('0049-123-456789'); // recommended

    /*
     * optional contact fields
     */
    $contact->setSalutation('Mr'); // Mr or Ms
    $contact->setTitle('Dr.');
    $contact->setGender('m');
    $contact->setUrlWebsite('example.com');
    $contact->setBirthplace('AnotherExampleCity');
    $contact->setNationality('german');

    /*
     * bank account of the merchant to get the money
     */
    $payout_account = new PaymentInformation();
    $payout_account->setIban("DE89370400440532013000");
    $payout_account->setBic(""); // recommended
    $payout_account->setOwner("Test #1");

    /*
     * Submitting the data to the API
     */
    $request_data = new PaymentContractsDTORequestId();
    $request_data->setContact($contact);
    $request_data->setPayinAccount(false);
    $request_data->setPayoutAccount($payout_account);
    $request_data->setProject("project_name " . time()); // must be unique for each request

    $response = $api_instance->requestId('me', $request_data); //"me" is a shortcut for the current contract of the API-user

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
