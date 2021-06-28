<?php

namespace Secuconnect\Demo\payment_with_smart_checkout;

use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Configuration;
use Secuconnect\Demo\Globals;

/**
 * Payment with Smart Checkout
 *
 * Step 1: Authenticate with OAuth 2.0
 *
 * @see <a href="https://developer.secuconnect.com/integration/Payment_with_Smart_Checkout.html">Payment with Smart Checkout</a>
 */
class Step1
{
    public static function main()
    {
        try {
            // init env
            Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secupay-ag.de/api/v2');
            Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secupay-ag.de/');

            // enable for using the live environment
//            Configuration::getDefaultConfiguration()->setHost('https://connect.secucard.com/api/v2');
//            Configuration::getDefaultConfiguration()->setAuthHost('https://connect.secucard.com/');

            // The authenticate() method will be called automatically on the first API call, so this optional:
            $accessToken = Authenticator::authenticateByClientCredentials(...array_values(Globals::OAuthClientCredentials));
            print_r($accessToken);
            /*
             * Sample output:
             * ==============
             * j9o39tmgb4j7gd76upautp1e56
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
