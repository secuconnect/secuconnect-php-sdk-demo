<?php
/** @noinspection PhpUnhandledExceptionInspection */

date_default_timezone_set('Europe/Berlin');

require_once __DIR__ . '/../../vendor/autoload.php';

use Secuconnect\Client\Api\GeneralContractsApi;
use Secuconnect\Client\Api\PaymentPayoutsApi;
use Secuconnect\Client\Api\PaymentSecupayCreditcardsApi;
use Secuconnect\Client\Api\PaymentSecupayDebitsApi;
use Secuconnect\Client\Api\PaymentSecupayInvoicesApi;
use Secuconnect\Client\Api\PaymentSecupayPrepaysApi;
use Secuconnect\Client\Api\PaymentSecupaySofortApi;
use Secuconnect\Client\Api\PaymentSubscriptionsApi;
use Secuconnect\Client\Api\PaymentTransactionsApi;
use Secuconnect\Client\Api\ServicesIdentrequestsApi;
use Secuconnect\Client\Api\ServicesIdentresultsApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Cache\FileCache;
use Secuconnect\Client\Configuration;
use Secuconnect\Demo\Globals;


try {
    // check IP-Address range and determine environment
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // FIXME - sample ip - for testing only
    $ip = '46.243.73.10';
    $packedIp = inet_pton($ip);
    if (!$packedIp) {
        echo "No REMOTE_ADDR given, can not validate IP-Address.\n";
        exit;
    }

    $env = null;
    foreach (
        [
            'production' => ['91.195.150.0', '91.195.151.255'],
            'testing' => ['46.243.73.0', '46.243.73.255'],
        ] as $range_name => $range
    ) {
        if ($packedIp > inet_pton($range[0]) && $packedIp < inet_pton($range[1])) {
            $env = $range_name;
            break;
        }
    }

    if ($env === null) {
        echo "Unknown REMOTE_ADDR, can not determine environment.\n";
        exit;
    }

    if ($env === 'production') {
        // change environment to live (as default is "testing")
        Configuration::getDefaultConfiguration()->setHost('https://connect.secucard.com/api/v2'); // live
        Configuration::getDefaultConfiguration()->setAuthHost('https://connect.secucard.com/'); // live
        $credentials = [
            'clientId' => '...',
            'clientSecret' => '...'
        ];
    } else {
        // (optional, as "testing" is already the default)
        Configuration::getDefaultConfiguration()->setHost('https://connect-testing.secuconnect.com/api/v2'); // demo server
        Configuration::getDefaultConfiguration()->setAuthHost('https://connect-testing.secuconnect.com/'); // demo server
        $credentials = Globals::OAuthClientCredentials;
    }

    // get push message
    $request = json_decode((string)file_get_contents('php://input'), true);
    // FIXME - sample input - for testing only
//    $request = json_decode('{
//            "object": "event.pushes",
//            "id": "evt_659486d09ffb726e4507f9c2",
//            "created": "2024-01-08T15:20:03+02:00",
//            "target": "payment.transactions",
//            "type": "changed",
//            "data": [
//                {
//                    "object": "payment.transactions",
//                    "id": "PCI_2U5DAY4JPVJRAAXV7FNQT248WA6AMG"
//                }
//            ]
//        }', true);
    // FIXME - sample input - for testing only
    $request = json_decode('{
            "object": "event.pushes",
            "id": "evt_658c0de980cfc4ad500adc90",
            "created": "2023-05-26T17:35:08+02:00",
            "target": "payment.secupaydebits",
            "type": "changed",
            "data": [
                {
                    "object": "payment.secupaydebits",
                    "id": "efkzchzllgek11062395"
                }
            ]
        }', true);

    if (empty($request['object']) || $request['object'] !== 'event.pushes') {
        echo "Not supported Push-Message format.\n";
        exit;
    }

    echo "Received Push-Message-ID {$request['id']} of type {$request['target']} for {$request['data'][0]['id']}.\n";
    /*
     * Sample output:
     * ==============
     * Received Push-Message-ID evt_d7a9cbd91c125ae409398b4e0e3199be of type payment.transactions for PCI_2U5DAY4JPVJRAAXV7FNQT248WA6AMG.
     */

    // check if the type should be processed
    $api_call = match ($request['target']) {
        'general.contracts' => ['class' => GeneralContractsApi::class, 'method' => 'getOne'],
        'payment.payouts' => ['class' => PaymentPayoutsApi::class, 'method' => 'getOne'],
        'payment.subscriptions' => ['class' => PaymentSubscriptionsApi::class, 'method' => 'paymentSubscriptionGetById'],
        'payment.transactions' => ['class' => PaymentTransactionsApi::class, 'method' => 'getOne'],
        'services.identrequests' => ['class' => ServicesIdentrequestsApi::class, 'method' => 'getOne'],
        'services.identresults' => ['class' => ServicesIdentresultsApi::class, 'method' => 'getOne'],
        // for old payment endpoints, not needed if you use only Smart.Transactions:
        'payment.secupaycreditcards' => ['class' => PaymentSecupayCreditcardsApi::class, 'method' => 'paymentSecupayCreditcardsGetById'],
        'payment.secupaydebits' => ['class' => PaymentSecupayDebitsApi::class, 'method' => 'paymentSecupayDebitsGetById'],
        'payment.secupayinvoices' => ['class' => PaymentSecupayInvoicesApi::class, 'method' => 'paymentSecupayInvoicesGetById'],
        'payment.secupayprepays' => ['class' => PaymentSecupayPrepaysApi::class, 'method' => 'paymentSecupayPrepaysGetById'],
        'payment.secupaysofort' => ['class' => PaymentSecupaySofortApi::class, 'method' => 'paymentSecupaySofortGetById'],
        default => null
    };

    // for TWINT, not needed if you use only Smart.Transactions:
    if ($request['target'] === 'payment.transactions' && !str_starts_with($request['data'][0]['id'], 'PCI_')) {
        $api_call = ['class' => PaymentTransactionsApi::class, 'method' => 'getPaymentTransactionsOldFormat'];
    }

    if (empty($api_call)) {
        // send HTTP 200 to stop receiving this message
        http_response_code(200);
        echo "Push-Message skipped (because of type).\n";
        exit;
    }

    // create cache storage
    $cache_dir = __DIR__ . '/../../.cache';
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0740, true);
    }
    Configuration::getDefaultConfiguration()->setCache(new FileCache($cache_dir));

    // check if the Push-Message-ID was processed already
    $cacheEntry = Configuration::getDefaultConfiguration()->getCache()->getItem('push_' . $request['id']);
    if ($cacheEntry->isHit()) {
        // send HTTP 200 to stop receiving this message
        http_response_code(200);
        echo "Push-Message was already processed.\n";
        exit;
    }

    // set credentials
    $token = Authenticator::authenticateByClientCredentials(
        ...$credentials
    );

    echo 'Auth token: ' . $token . "\n";
    /*
     * Sample output:
     * ==============
     * Auth token: dprfdtmuttqhm6k1omm7bnl7d0
     */

    // get the current data from API
    $id = $request['data'][0]['id'];
    $data = (new $api_call['class'])->{$api_call['method']}($id);
    echo "Current data for $id: " . print_r($data->__toString(), true) . "\n";

    /*
     * Sample output:
     * ==============
     * Current data for PCI_2U5DAY4JPVJRAAXV7FNQT248WA6AMG: {
     *    ...
     * }
     */

    // FIXME Now you should execute your custom code in some background process.
    sleep(10); // send response as early as possible, after 30 seconds the push will be tagged as failed!

    // update storage for received push messages and return HTTP 200.
    http_response_code(200);
    echo "Push-Message successfully processed.\n";
    $cacheEntry->set(time());
    $cacheEntry->expiresAt(new DateTime('+1year'));
    Configuration::getDefaultConfiguration()->getCache()->save($cacheEntry);
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}