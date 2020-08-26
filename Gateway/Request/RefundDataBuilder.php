<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Apexx\Applepay\Helper\Data as ApplePayHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;

/**
 * Class RefundDataBuilder
 * @package Apexx\Applepay\Gateway\Request
 */
class RefundDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ApplePayHelper
     */
    protected  $applepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * RefundDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param ApplePayHelper $applepayHelper
     * @param ApexxBaseHelper $apexxBaseHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        ApplePayHelper $applepayHelper,
        ApexxBaseHelper $apexxBaseHelper
    )
    {
        $this->subjectReader = $subjectReader;
        $this->applepayHelper = $applepayHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();

        // Send Parameters to Paypal Payment Client
        $order = $paymentDO->getOrder();
        $amount = $buildSubject['amount'];

        //Get last transaction id for authorization
        $lastTransId = $this->apexxBaseHelper->getHostedPayTxnId($order->getId());

        if ($lastTransId != '') {
            $requestData = [
                "transactionId" => $lastTransId,
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId(),
                "capture_id" => $orderPayment->getParentTransactionId()
            ];
        } else {
            $requestData = [
                "transactionId" => $orderPayment->getParentTransactionId(),
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId()
            ];
        }

        return $requestData;
    }
}
