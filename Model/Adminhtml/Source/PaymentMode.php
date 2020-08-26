<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Model\Adminhtml\Source;

/**
 * Class PaymentMode
 * @package Apexx\Applepay\Model\Adminhtml\Source
 */
class PaymentMode
{
    public function toOptionArray()
    {
        return [
                    ['value' => 'TEST', 'label' => __('Test')],
                    ['value' => 'LIVE', 'label' => __('Live')],
        ];
    }
}
