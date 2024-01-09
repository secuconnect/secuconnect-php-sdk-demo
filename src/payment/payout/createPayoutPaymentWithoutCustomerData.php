<?php

namespace Secuconnect\Demo;

require_once __DIR__ . '/../../../vendor/autoload.php';

use DateTime;
use Exception;
use Secuconnect\Client\Api\PaymentSecupayPayoutApi;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Model\BankAccountDescriptor;
use Secuconnect\Client\Model\SecupayPayoutListItem;
use Secuconnect\Client\Model\SecupayPayoutWithoutCustomerDTO;

try {
    Authenticator::authenticateByClientCredentials(
        ...Globals::OAuthClientCredentials
    );

    $transaction = new SecupayPayoutWithoutCustomerDTO();
    $transaction->setCurrency('EUR');
    $transaction->setContractId('GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0');

    $transaction->setPurpose('Payout Test #2');

    // optional - delay excretion time:
    $transaction->setExecutionDate((new DateTime('next tuesday'))->format('Y-m-d'));

    $listItem1 = new SecupayPayoutListItem();
    $listItem1->setReference('2000.1');
    $listItem1->setPurpose('Payout to bank account');
    $listItem1->setAmount(200); // in euro-cent
    $bankAccount1 = new BankAccountDescriptor();
    $bankAccount1->setOwner('Max Mustermann');
    $bankAccount1->setIban('DE37503240001000000524');
    $listItem1->setBankAccount($bankAccount1);

    $listItem2 = new SecupayPayoutListItem();
    $listItem2->setReference('2000.2');
    $listItem2->setPurpose('Payout to bank account with reference to merchant (by MRC)');
    $listItem2->setRecipient('MRC_W28RCJM74AGXDFDRTV0AR748G4PGN0');
    $listItem2->setAmount(60); // in euro-cent
    $bankAccount2 = new BankAccountDescriptor();
    $bankAccount2->setOwner('Jan Novák');
    $bankAccount2->setIban('CZ4201000000195505030267');
    $listItem2->setBankAccount($bankAccount2);

    $listItem3 = new SecupayPayoutListItem();
    $listItem3->setReference('2000.3');
    $listItem3->setPurpose('Payout to bank account with reference to customer/merchant (by ID)');
    $listItem3->setRecipient('11026600');
    $listItem3->setAmount(40); // in euro-cent
    $bankAccount3 = new BankAccountDescriptor();
    $bankAccount3->setOwner('Jan Jansen');
    $bankAccount3->setIban('NL18ABNA0484869868');
    $listItem3->setBankAccount($bankAccount3);

    $listItem4 = new SecupayPayoutListItem();
    $listItem4->setReference('2000.4');
    $listItem4->setPurpose('Payout to bank account with reference to payment transaction (the investment)');
    $listItem4->setOriginTransaction(110152028);
    $listItem4->setAmount(50); // in euro-cent
    $bankAccount4 = new BankAccountDescriptor();
    $bankAccount4->setOwner('Jan Kowalski');
    $bankAccount4->setIban('PL37109024020000000610000434');
    $listItem4->setBankAccount($bankAccount4);

    $transaction->setPayoutList(
        [
            $listItem1,
            $listItem2,
            $listItem3,
            $listItem4,
        ]
    );

    // calculate the amount
    $amount = 0;
    foreach ($transaction->getPayoutList() as $item) {
        $amount += (int)$item->getAmount();
    }
    $transaction->setAmount($amount); // in euro-cent

    $api_instance = new PaymentSecupayPayoutApi();
    $response = $api_instance->paymentSecupaypayoutWithoutCustomerPost($transaction);

    print_r($response);
    /*
     * Sample output:
     * ==============
     * Secuconnect\Client\Model\SecupayPayoutWithoutCustomerResponse Object
     * (
     *     [container:protected] => Array
     *         (
     *             [amount] => 350
     *             [currency] => EUR
     *             [contract_id] => GCR_2H69XY35227V2VKP9WRA3SJ0W95RP0
     *             [payout_list] => Array
     *                 (
     *                     [0] => Secuconnect\Client\Model\SecupayPayoutListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [amount] => 200
     *                                     [purpose] => Payout to bank account
     *                                     [bank_account] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [iban] => DE37503240001000000524
     *                                                     [bic] => FTSBDEFAXXX
     *                                                     [owner] => Max Mustermann
     *                                                     [bankname] => ABN AMRO Bank, Frankfurt Branch
     *                                                 )
     *                                         )
     *                                     [trans_id] => 110152052
     *                                 )
     *                         )
     *                     [1] => Secuconnect\Client\Model\SecupayPayoutListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [amount] => 60
     *                                     [purpose] => Payout to bank account with reference to merchant (by MRC)
     *                                     [bank_account] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [iban] => CZ4201000000195505030267
     *                                                     [bic] => KOMBCZPP
     *                                                     [owner] => Jan Novák
     *                                                     [bankname] => Komerční banka, a.s.
     *                                                 )
     *                                         )
     *                                     [trans_id] => 110152053
     *                                 )
     *                         )
     *                     [2] => Secuconnect\Client\Model\SecupayPayoutListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [amount] => 40
     *                                     [purpose] => Payout to bank account with reference to customer/merchant (by ID)
     *                                     [bank_account] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [iban] => NL18ABNA0484869868
     *                                                     [bic] => ABNANL2A
     *                                                     [owner] => Jan Jansen
     *                                                     [bankname] => ABN AMRO Bank N.V.
     *                                                 )
     *                                         )
     *                                     [trans_id] => 110152054
     *                                 )
     *                         )
     *                     [3] => Secuconnect\Client\Model\SecupayPayoutListItem Object
     *                         (
     *                             [container:protected] => Array
     *                                 (
     *                                     [amount] => 50
     *                                     [purpose] => Payout to bank account with reference to payment transaction (the investment)
     *                                     [bank_account] => Secuconnect\Client\Model\BankAccountDescriptor Object
     *                                         (
     *                                             [container:protected] => Array
     *                                                 (
     *                                                     [iban] => PL37109024020000000610000434
     *                                                     [bic] => WBKPPLPPXXX
     *                                                     [owner] => Jan Kowalski
     *                                                     [bankname] => Santander Bank Polska Spółka Akcyjna
     *                                                 )
     *                                         )
     *                                     [origin_transaction] => 110152028
     *                                     [trans_id] => 110152055
     *                                 )
     *                         )
     *                 )
     *             [purpose] => Payout Test #2
     *             [execution_date] => 2024-01-16
     *             [trans_id] => 110152051
     *             [transfer_account] => Secuconnect\Client\Model\PaymentInformation Object
     *                 (
     *                     [container:protected] => Array
     *                         (
     *                             [iban] => DE81850400611005523759
     *                             [bic] => COBADEFFXXX
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
