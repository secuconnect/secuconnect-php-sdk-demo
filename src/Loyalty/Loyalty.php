<?php
/** @noinspection PhpUnreachableStatementInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection DuplicatedCode */
/** @noinspection PhpIllegalPsrClassPathInspection */
/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

namespace Secuconnect\Demo\Loyalty;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;
use JetBrains\PhpStorm\NoReturn;
use Secuconnect\Client\Api\LoyaltyMerchantCardsApi;
use Secuconnect\Client\Api\SmartTransactionsApi;
use Secuconnect\Client\ApiClient;
use Secuconnect\Client\ApiException;
use Secuconnect\Client\Authentication\Authenticator;
use Secuconnect\Client\Cache\FileCache;
use Secuconnect\Client\Configuration;
use Secuconnect\Client\Model\LoyaltyMerchantcardsDTOCardBalanceReceipt;
use Secuconnect\Client\Model\LoyaltyMerchantcardsDTOValidateMerchantCard;
use Secuconnect\Client\Model\LoyaltyMerchantcardsProductModel;
use Secuconnect\Client\Model\SmartTransactionsBasket;
use Secuconnect\Client\Model\SmartTransactionsBasketInfo;
use Secuconnect\Client\Model\SmartTransactionsBasketProduct;
use Secuconnect\Client\Model\SmartTransactionsDTO;
use Secuconnect\Client\Model\SmartTransactionsIdent;
use Secuconnect\Client\Model\SmartTransactionsProductModel;

try {
    $secuconnect = new SecuconnectClient('test');

    $card_number = '927600442...';
    $loyalty_card = new LoyaltyCard($secuconnect, $card_number);
    print_r($loyalty_card->getCardDetails());
    sleep(3);

    $stx_1 = $loyalty_card->charge(1000);
    print_r($stx_1->getId());
    print_r($stx_1->getStatus());
    sleep(3);

    $stx_1 = $loyalty_card->cancel($stx_1->getId());
    print_r($stx_1->getId());
    print_r($stx_1->getStatus());
    sleep(3);

    $loyalty_card = new LoyaltyCard($secuconnect, $card_number);
    print_r($loyalty_card->refreshCardDetails());
    sleep(3);

    $stx_2 = $loyalty_card->pay(759);
    print_r($stx_2->getId());
    print_r($stx_2->getStatus());
    sleep(3);

    $stx_2 = $loyalty_card->cancel($stx_2->getId());
    print_r($stx_2->getId());
    print_r($stx_2->getStatus());
    sleep(3);


    $loyalty_card = new LoyaltyCard($secuconnect, $card_number);
    print_r($loyalty_card->refreshCardDetails());
} catch (ApiException $e) {
    echo $e->getTraceAsString();
    print_r($e->getResponseBody());

    $supportId = '';
    if (isset($e->getResponseBody()->supportId)) {
        $supportId = ' Support-ID: ' . $e->getResponseBody()->supportId;
    }

    throw new Exception('Request was not successful, check the log for details.' . $supportId);
}


/**
 * Class ErrorCodes
 */
class ErrorCodes
{
    const INCORRECT_CARD_NUMBER = 14;
    const INVALID_DATA = 27;
    const CARD_LOCKED = 41;
    const INSUFFICIENT_CREDIT = 51;
    const INVALID_REQUEST_FORMAT = 56;
    const DEVICE_NOT_FOUND = 83;
    const DEVICE_NOT_ALLOWED = 84;
    const UNKNOWN = 85;
}


/**
 * Class SecuconnectClient
 */
class SecuconnectClient
{
    const CLIENT_ID = '...';
    const CLIENT_SECRET = '...';
    const UUID_PREFIX = '/vendor/apidevices/uuid/';

    const HOST_TESTING = 'https://connect-testing.secuconnect.com/api/v2';
    const AUTH_HOST_TESTING = 'https://connect-testing.secuconnect.com/';

    const HOST_LIVE = 'https://connect.secucard.com/api/v2';
    const AUTH_HOST_LIVE = 'https://connect.secucard.com/';

    /**
     * @var ApiClient
     */
    private ApiClient $client;

    /**
     * @param string $uuid
     * @throws ApiException
     */
    public function __construct(string $uuid)
    {
        Configuration::setDefaultConfiguration(new Configuration(
            new FileCache(__DIR__ . '\\..\\..\\.cache\\')
        ));

        // Change environment to "testing" (which is the default in the SDK)
        Configuration::getDefaultConfiguration()->setHost(self::HOST_TESTING);
        Configuration::getDefaultConfiguration()->setAuthHost(self::AUTH_HOST_TESTING);

        // Change environment to "live"
//        Configuration::getDefaultConfiguration()->setHost(self::HOST_LIVE);
//        Configuration::getDefaultConfiguration()->setAuthHost(self::AUTH_HOST_LIVE);

        Authenticator::authenticateByDevice(
            self::CLIENT_ID,
            self::CLIENT_SECRET,
            self::UUID_PREFIX . $uuid
        );

        $this->client = new ApiClient();
    }

    public function refreshToken(): void
    {
        Authenticator::reauthenticate();
    }

    /**
     * @return ApiClient
     */
    public function getClient(): ApiClient
    {
        return $this->client;
    }
}

/**
 * Class LoyaltyCard
 */
class LoyaltyCard
{
    private SecuconnectClient $secuconnect;

    private LoyaltyMerchantcardsProductModel $merchant_card;

    /**
     * Loyalty constructor.
     * @param SecuconnectClient $secuconnect
     * @param string $card_number
     * @throws ApiException
     */
    public function __construct(SecuconnectClient $secuconnect, string $card_number)
    {
        $this->secuconnect = $secuconnect;
        $this->validateCardNumber($card_number);
    }

    /**
     * @param string $card_number
     * @return bool
     * @throws ApiException
     */
    protected function validateCardNumber(string $card_number): bool
    {
        $endpoint = new LoyaltyMerchantCardsApi($this->secuconnect->getClient());

        $validate_merchant_card = $endpoint->validateMerchantCard(
            'me',
            new LoyaltyMerchantcardsDTOValidateMerchantCard(['cardnumber' => $card_number])
        );

        if (!$validate_merchant_card->getIsValid()) {
            $this->addError('incorrect / unknown merchantcard', ErrorCodes::INCORRECT_CARD_NUMBER);
            return false;
        }

        if ($validate_merchant_card->getIsLocked()) {
            $this->addError('card is locked', ErrorCodes::CARD_LOCKED);
            return false;
        }

        $merchantcards_list = $endpoint->getAll(
            1,
            null,
            null,
            "card.cardnumber:\"$card_number\""
        );
        $this->merchant_card = $merchantcards_list->getData()[0];

        return true;
    }

    /**
     * @param $message
     * @param $code
     */
    #[NoReturn]
    private function addError($message, $code): void
    {
        echo 'ERROR: ' . $message . ' (Code: ' . $code . ')';
        die();
    }

    public function getCardDetails(): LoyaltyMerchantcardsProductModel
    {
        return $this->merchant_card;
    }

    public function refreshCardDetails(): ?LoyaltyMerchantcardsProductModel
    {
        if (empty($this->merchant_card)) {
            $this->addError('invalid input', ErrorCodes::INVALID_DATA);
            return null;
        }

        $endpoint = new LoyaltyMerchantCardsApi($this->secuconnect->getClient());
        $this->merchant_card = $endpoint->getOne($this->merchant_card->getId());
        return $this->merchant_card;
    }

    /**
     * @param int $amount price in euro cent
     * @return SmartTransactionsProductModel|null
     * @throws ApiException
     * @noinspection PhpParamsInspection
     */
    public function pay(int $amount): ?SmartTransactionsProductModel
    {
        if (empty($this->merchant_card) || $amount <= 0) {
            $this->addError('invalid input', ErrorCodes::INVALID_DATA);
            return null;
        }

        if ($this->validateBalance($amount)) {
            $ident = new SmartTransactionsIdent();
            $ident->setType('card');
            $ident->setValue($this->merchant_card->getCard()->getCardnumber());

            $basket_item = new SmartTransactionsBasketProduct();
            $basket_item->setId(0);
            $basket_item->setEan('4260447149533'); // One of '4260447149533', '4260447149540' for discharge a card
            $basket_item->setDesc('Entladung ' . number_format($amount / 100, 2, ',', '') . ' €');
            $basket_item->setQuantity(1);
            $basket_item->setPriceOne($amount);
            $basket_item->setTax(0);

            $basket = new SmartTransactionsBasket();
            $basket->setProducts([$basket_item]);

            $basket_info = new SmartTransactionsBasketInfo();
            $basket_info->setSum($amount);
            $basket_info->setCurrency("EUR");

            $transaction = new SmartTransactionsDTO();
            $transaction->setIdents([$ident]);
            $transaction->setBasket($basket);
            $transaction->setBasketInfo($basket_info);

            $api_instance = new SmartTransactionsApi();
            $stx = $api_instance->addTransaction($transaction);

            return $api_instance->startTransaction($stx->getId(), 'loyalty', null);
        }

        return null;
    }

    /**
     * @param int $discharge_amount
     * @return bool
     * @throws ApiException
     */
    protected function validateBalance(int $discharge_amount): bool
    {
        if (empty($this->merchant_card)) {
            return false;
        }

        // get configured limit data for merchant card
        $endpoint = new LoyaltyMerchantCardsApi($this->secuconnect->getClient());

        $payload = new LoyaltyMerchantcardsDTOCardBalanceReceipt();
        $payload->setCardnumber($this->merchant_card->getCard()->getCardnumber());

        // get card balance info
        $limit_data = $endpoint->cardBalanceReceipt($this->merchant_card->getId(), $payload);

        // set amount split enable property to process this on execTransactions request
        $amount_split_enabled = $limit_data->getAmountSplitEnabled();

        // unlimited
        if ($limit_data->getLimitAllowed() && $limit_data->getLimit() === 0) {
            return true;
        }

        $balance = $limit_data->getBalance();

        // if there is a limit, add it to the balance
        if ($limit_data->getLimitAllowed()) {
            $balance += $limit_data->getLimit();
        }

        // no limit data defined
        if ($balance < $discharge_amount && !$amount_split_enabled) {
            $this->addError('insufficient credit', ErrorCodes::INSUFFICIENT_CREDIT);
            return false;
        }

        // if no limit is allowed and card balance is 0,
        // add an error even if amount_split_enabled because no TA will be created
        // this check also includes limit data (if limit is already reached)
        if ($balance === 0) {
            $this->addError('insufficient credit', ErrorCodes::INSUFFICIENT_CREDIT);
            return false;
        }

        return true;
    }

    /**
     * @param int $amount price in euro cent
     * @return SmartTransactionsProductModel|null
     * @throws ApiException
     */
    public function charge(int $amount): ?SmartTransactionsProductModel
    {
        if (empty($this->merchant_card) || $amount <= 0) {
            $this->addError('invalid input', ErrorCodes::INVALID_DATA);
            return null;
        }

        $ident = new SmartTransactionsIdent();
        $ident->setType('card');
        $ident->setValue($this->merchant_card->getCard()->getCardnumber());

        $basket_item = new SmartTransactionsBasketProduct();
        $basket_item->setId(0);
        $basket_item->setEan('4260447149502'); // One of '4260447149502', '4260447149519' for charge a card
        $basket_item->setDesc('Aufladung ' . number_format($amount / 100, 2, ',', '') . ' €');
        $basket_item->setQuantity(1);
        $basket_item->setPriceOne($amount);
        $basket_item->setTax(0);

        $basket = new SmartTransactionsBasket();
        $basket->setProducts([$basket_item]);

        $basket_info = new SmartTransactionsBasketInfo();
        $basket_info->setSum($amount);
        $basket_info->setCurrency("EUR");

        $transaction = new SmartTransactionsDTO();
        $transaction->setIdents([$ident]);
        $transaction->setBasket($basket);
        $transaction->setBasketInfo($basket_info);

        $api_instance = new SmartTransactionsApi();
        $stx = $api_instance->addTransaction($transaction);

        /** @noinspection PhpParamsInspection */
        return $api_instance->startTransaction($stx->getId(), 'cash', null);
    }

    /**
     * @param string $stx_id
     * @return SmartTransactionsProductModel|null
     * @throws ApiException
     */
    public function cancel(string $stx_id): ?SmartTransactionsProductModel
    {
        if (empty($stx_id)) {
            $this->addError('invalid input', ErrorCodes::INVALID_DATA);
            return null;
        }

        $api_instance = new SmartTransactionsApi();
        return $api_instance->cancelTransaction($stx_id);
    }
}
