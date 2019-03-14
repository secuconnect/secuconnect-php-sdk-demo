<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ReceiptParser.php';
$json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sample.json');
$data = json_decode($json, true);
echo ReceiptParser::parseReceipt($data);
