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
 * Class RefundSale
 * @package Apexx\Applepay\Gateway\Http\Client
 */
class RefundSale implements ClientInterface
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
     * @var ApplePayHelper
     */
    protected  $applepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * RefundSale constructor.
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
        $request = $transferObject->getBody();

        // Set refund url
        $url = $this->apexxBaseHelper->getApiEndpoint().'refund/'.$request['transactionId'];
        unset($request['transactionId']);
        //Set parameters for curl
        $resultCode = json_encode($request);
        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);

        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Applepay Refund Request:', $request);
        $this->customLogger->debug('Applepay Refund Response:', $responseResult);

        return $responseResult;
    }
}
