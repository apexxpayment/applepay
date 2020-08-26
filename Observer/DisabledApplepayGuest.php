<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Customer\Model\Session;
use \Magento\Customer\Api\CustomerRepositoryInterface;

class DisabledApplepayGuest implements ObserverInterface
{
    /**
     * @var Session
     */
	protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * DisabledApplepayGuest constructor.
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
	public function __construct(
	    Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
		$this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
	}


	public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
        $result = $observer->getEvent()->getResult();

        if ($this->customerSession->isLoggedIn()) {
            if ($paymentMethod == 'applepay_gateway') {
                $result->setData('is_available', true);
                return;
            }
        } else {
            if ($paymentMethod == 'applepay_gateway') {
                $result->setData('is_available', false);
                return;
            }
        }

    }
}
