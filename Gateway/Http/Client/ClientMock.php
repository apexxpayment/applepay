<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class ClientMock
 * @package Apexx\Applepay\Gateway\Http\Client
 */
class ClientMock implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];
    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var ApplePayHelper
     */
    protected  $applepayHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * ClientMock constructor.
     * @param Curl $curl
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param ApplePayHelper $applepayHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        Curl $curl,
        ApexxBaseHelper $apexxBaseHelper,
        ApplePayHelper $applepayHelper,
        CustomLogger $customLogger
    ) {
        $this->curlClient = $curl;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->applepayHelper = $applepayHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $url = $this->apexxBaseHelper->getApiEndpoint().'payment/direct';

        $resultCode = json_encode($transferObject->getBody());

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);

        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Applepay Authorize Request:', $transferObject->getBody());
        $this->customLogger->debug('Applepay Authorize Response:', $responseResult);

        return $responseResult;
    }
}
