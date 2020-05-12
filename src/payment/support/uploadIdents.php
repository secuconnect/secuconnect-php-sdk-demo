<?php

namespace Secuconnect\Demo;

require __DIR__ . '/../../../vendor/autoload.php';

use Exception;
use Secuconnect\Client\Api\DocumentUploadsApi;
use Secuconnect\Client\Api\ServicesUploadidentsProductApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\DocumentUploadsDTOContent;
use Secuconnect\Client\Model\UploadidentsProductDTO;

try {
    Authenticator::authenticateByClientCredentials(
        '...',
        '...'
    );

    /*
     * Part 1: upload files
     */
    $api = new DocumentUploadsApi();
    $data = new DocumentUploadsDTOContent();
    $data->setContent(
        base64_encode(
            file_get_contents(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'LICENSE'
            )
        )
    );

    $response = $api->documentUploadsPost($data);

    print_r($response);
    /*
     * Secuconnect\Client\Model\DocumentUploadsBaseProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [object] => document.uploads
     *             [id] => DUP_3NZDYPVBGZ4K5QE6MDG38YNQKW7C4A
     *             [created] => 2020-05-12T08:39:45+02:00
     *         )
     * )
     */

    $dup_id = $response->getId();
    unset($api, $data, $response);

    /*
     * Part 2: create support case with file ids
     */
    $api = new ServicesUploadidentsProductApi();
    $data = new UploadidentsProductDTO();
    $data->setApikey("37373c132df0299c5bdcf7c7638dd47aa41a2fe2");
    $data->setDocumentIds([$dup_id]);

    $response = $api->addUploadidents($data);

    print_r($response);
    /*
     * Secuconnect\Client\Model\UploadidentsProductModel Object
     * (
     *     [container:protected] => Array
     *         (
     *             [service_issue_id] => 1211770
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
