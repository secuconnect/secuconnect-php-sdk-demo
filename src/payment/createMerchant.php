<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContractsDTORequestId;
use Secuconnect\Client\Model\PaymentInformation;

try {

    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    $api_instance = new \Secuconnect\Client\Api\PaymentContractsApi();

    $adress = new Address();
    $adress->setType('invoice');
    $adress->setStreet('example street');
    $adress->setStreetNumber('6a');
    $adress->setPostalCode('01234');
    $adress->setCity('Testcity');
    $adress->setCountry('Germany');

    $contact = new Contact();
    $contact->setSalutation('Mr.');
    $contact->setTitle('Dr.');
    $contact->setForename('John');
    $contact->setSurname('Doe');
    $contact->setCompanyname('Example Inc.');
    $contact->setGender('m');
    $contact->setDob('1901-02-03');
    $contact->setUrlWebsite('example.com');
    $contact->setBirthplace('AnotherExampleCity');
    $contact->setNationality('german');
    $contact->setAddress($adress);
    $contact->setEmail('example@example.com');
    $contact->setPhone('0049-123-456789');

    $payout_account = new PaymentInformation();
    $payout_account->setIban("DE89370400440532013000");
    $payout_account->setBic("");
    $payout_account->setOwner("Test #1");

    $request_data = new PaymentContractsDTORequestId();
    $request_data->setContact($contact);
    $request_data->setPayinAccount(false);
    $request_data->setPayoutAccount($payout_account);
    $request_data->setProject("project_name ".time());

    $response = $api_instance->requestId('me', $request_data); //"me" is a shortcut for the current contract of the apiuser

    print_r($response);

} catch (ApiException $e) {

    var_dump($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new \Exception('Request was not successful, check the log for details.' . $supportId);
}
