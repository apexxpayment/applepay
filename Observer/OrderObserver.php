<?php
/**
 * Custom payment method in Magento 2
 * @category    Applepay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Apexx\Applepay\Helper\Data as configHelper;


/**
 * Class OrderObserver
 * @package Apexx\Applepay\Observer
 */

class OrderObserver extends AbstractDataAssignObserver
{
    /**
     * @var configHelper
     */
    protected  $configHelper;

    /**
     * @param Observer $observer
     */

    public function __construct(
        configHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $eventName = $observer->getEvent()->getName();
        $method = $order->getPayment()->getMethod();
        $paymentmode = $this->configHelper->getApplepayPaymentAction();
        if ($method == 'applepay_gateway' && $paymentmode == 'false') {
            switch ($eventName) {
                case 'sales_order_place_after':
                    $this->updateOrderState($observer);

                    break;
            }
        }
    }

    /**
     * @param $observer
     */
    public function updateOrderState($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $order->setStatus('authorised');
        //$order->setIsNotified(false);
        $order->save();
    }
}
