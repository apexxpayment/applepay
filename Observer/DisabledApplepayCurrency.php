<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session As CheckoutSession;
use Apexx\Applepay\Helper\Data As ApplepayHelper;

/**
 * Class DisabledApplepayCurrency
 * @package Apexx\Applepay\Observer
 */
class DisabledApplepayCurrency implements ObserverInterface
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
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ApplepayHelper
     */
    protected $applePayHelper;

    /**
     * DisabledPaypalCurrency constructor.
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param ApplepayHelper $applePayHelper
     */
	public function __construct(
	    Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        ApplepayHelper $applePayHelper
    ) {
		$this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->applePayHelper = $applePayHelper;
	}

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
        $result = $observer->getEvent()->getResult();

        $quoteCurrency = $this->checkoutSession->getQuote()->getQuoteCurrencyCode();
        $allowCurrency = $this->applePayHelper->getAllowPaymentCurrency($quoteCurrency); 

        if ($this->customerSession->isLoggedIn()) {
            if ($paymentMethod == 'applepay_gateway') {
                if (!empty($allowCurrency)) {
                    $result->setData('is_available', true);
                    return;
                } else {
                    $result->setData('is_available', false);
                    return;
                }
            }
        } else {
            if ($paymentMethod == 'applepay_gateway') {
             if (!empty($allowCurrency)) {
                    $result->setData('is_available', true);
                    return;
                } else {
                    $result->setData('is_available', false);
                    return;
                }
            }
        }
    }
}
