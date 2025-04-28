<?php

namespace Secuconnect\Demo\api_integration_of_smart_checkout;

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;
use Secuconnect\Demo\Globals;

/**
 * API Integration of Smart Checkout
 *
 * Step 1: Authenticate with OAuth 2.0
 *
 * @see <a href="https://developer.secuconnect.com/integration/API_Integration_of_Smart_Checkout.html">API Integration of Smart Checkout</a>
 */
class Step1
{
    public static function main()
    {
        try {
            // init env
            Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secuconnect.com/api/v2');
            Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secuconnect.com/');

            // enable for using the live environment
//            Configuration::getDefaultConfiguration()->setHost('https://connect.secucard.com/api/v2');
//            Configuration::getDefaultConfiguration()->setAuthHost('https://connect.secucard.com/');

            // The authenticate() method will be called automatically on the first API call, so this optional:
            $accessToken = Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));
            print_r($accessToken);
            /*
             * Sample output:
             * ==============
             * fdjt28qq8qr7b0s9epcvesnq33
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
            /*
             * Sample output:
             * ==============
             * ERROR: {"error":"invalid_client","error_description":"The client credentials are invalid"}
             */
        }
    }
}

require_once __DIR__ . '/../../vendor/autoload.php';
Step1::main();
