<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class CancelSale
 * @package Apexx\Applepay\Gateway\Http\Client
 */
class CancelSale implements ClientInterface
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
     * CancelSale constructor.
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
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
     public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        // Set cancel url
        $url = $this->apexxBaseHelper->getApiEndpoint().$request['transactionId'].'/cancel';
        unset($request['transactionId']);
       // 
        //Set parameters for curl
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);
        $this->customLogger->debug('Applepay Cancel Request:', $request);
        $this->customLogger->debug('Applepay Cancel Response:', $responseResult);

        return $responseResult;
    }
}
