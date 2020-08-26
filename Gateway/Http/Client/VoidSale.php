<?php
/**
 * Custom payment method in Magento 2
 * @category    CcDirect
 * @package     Apexx_CcDirect
 */
namespace Apexx\Applepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class VoidSale
 * @package Apexx\Applepay\Gateway\Http\Client
 */
class VoidSale implements ClientInterface
{
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
     * VoidSale constructor.
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param ApplePayHelper $applepayHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        ApexxBaseHelper $apexxBaseHelper,
        ApplePayHelper $applepayHelper,
        CustomLogger $customLogger
    ) {
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->applepayHelper = $applepayHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array|mixed
     */
     public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();
        // Set void url
        $url = $this->apexxBaseHelper->getApiEndpoint().$request['transactionId'].'/cancel';
        unset($request['transactionId']);
        //Set parameters for curl
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Applepay Void Request:', $request);
        $this->customLogger->debug('Applepay Void Response:', $responseResult);

        return $responseResult;
    }
}
