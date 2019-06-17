<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\PaymentCustomersApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;

/*
 * Search for an existing customer
 */
try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    // Set a email filter (schema: {fieldname}:{value} )
    $query = 'contact.email:' . urlencode('example123@example.com'); // values MUST be url encoded

    // Add a name filter
    $query .= ' AND contact.surname:' . urlencode('DOE'); // The search is case-insensitive
    $query .= ' AND contact.forename:' . urlencode('John');

    // This also works:
    // $query .= ' AND contact.forename:' . urlencode('J') . '*' . urlencode('n'); // The astrix is a wildcard for any number of characters
    // $query .= ' AND contact.forename:' . urlencode('J') . '??' . urlencode('n'); // The question mark is a wildcard for one character

    // Run the API request
    $api_instance = new PaymentCustomersApi();
    $response = $api_instance->paymentCustomersGet(
        1, // Return only one customer object
        null,
        null,
        $query
    );

    print_r($response);

    // You should maybe check if there was more then one customer with this data.
    if ($response->getCount() > 1) {
        echo 'WARNING: there was more than one customer found.';
    }

    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\PaymentCustomersList Object
     * (
     *     [container:protected] => Array
     *         (
     *             [count] => 1
     *             [data] => Array
     *                 (
     *                     [0] => Secuconnect\Client\Model\PaymentCustomersProductModel Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [object] => payment.customers
     *                                     [id] => PCU_...
     *                                     [contract] => Secuconnect\Client\Model\ProductInstanceUID Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [object] => payment.contracts
     *                                                     [id] => PCR_...
     *                                                 )
     *                                         )
     *                                     [contact] => Secuconnect\Client\Model\Contact Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [forename] => John
     *                                                     [surname] => Doe
     *                                                     [companyname] => Example Inc.
     *                                                     [salutation] => Mr.
     *                                                     [gender] => m
     *                                                     [title] => Dr.
     *                                                     [email] => example123@example.com
     *                                                     [phone] => 0049-123-456789
     *                                                     [dob] => 1901-02-03T00:00:00+01:00
     *                                                     [picture] =>
     *                                                     [url_website] => example.com
     *                                                     [birthplace] => AnotherExampleCity
     *                                                     [nationality] => german
     *                                                     [address] => Secuconnect\Client\Model\Address Object
     *                                                         (
     *                                                             [container:protected] => Array
     *                                                                 (
     *                                                                     [street] => example street
     *                                                                     [street_number] => 6a
     *                                                                     [city] => Testcity
     *                                                                     [postal_code] => 01234
     *                                                                     [country] => DE
     *                                                                 )
     *                                                         )
     *                                                 )
     *                                         )
     *                                     [created] => 2019-05-24T10:41:34+02:00
     *                                 )
     *                         )
     *                 )
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
