<?php
/** @noinspection PhpUnhandledExceptionInspection */

require __DIR__ . '/init.php';

use Secuconnect\Client\Api\PaymentContractsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Model\Address;
use Secuconnect\Client\Model\Contact;
use Secuconnect\Client\Model\PaymentContractsDTOIFrameOpts;
use Secuconnect\Client\Model\PaymentContractsDTORequestId;
use Secuconnect\Client\Model\PaymentInformation;

try {
    $contact = new Contact();
    $contact->setSalutation('Herr');
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

    $project = new PaymentContractsDTORequestId();
    $project->setContact($contact);
    $project->setProject('Feuerwerk 2023/2024');
    $project->setPayoutPurpose('Auszahlung deines Projekts: Feuerwerk 2023/2024');

    $bank_account = new PaymentInformation();
    $bank_account->setOwner('Max Mustermann');
    $bank_account->setIban('DE37503240001000000524');
    $project->setPayoutAccount($bank_account);

    $iframe_opts = new PaymentContractsDTOIFrameOpts();
    $iframe_opts->setShowBasket(true);
    $iframe_opts->setBasketTitle('Unterstützung für Feuerwerk 2023/2024');
    $iframe_opts->setSubmitButtonTitle('Jetzt zahlungspflichtig unterstützen');
    $iframe_opts->setCession('personal');
    $project->setIframeOpts($iframe_opts);

    $project->setPayinAccount(true);

    $project = (new PaymentContractsApi())->requestId('me', $project);

    if ($project->getContract()?->getId()) {
        echo 'Created project with id: ' . $project->getContract()?->getId() . "\n";
        echo 'Project data: ' . print_r($project->__toString(), true) . "\n";
    } else {
        echo 'Project creation failed' . "\n";
        exit;
    }

    /*
     * Sample output:
     * ==============
     * Created project with id: GCR_90YFZP3Q545BEHVFPQZWE769W20WOW
     * Project data: {
     * ...
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
