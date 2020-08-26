<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Model\Adminhtml\Source;

/**
 * Class CustomerType
 * @package Apexx\Applepay\Model\Adminhtml\Source
 */
class CustomerType
{
    /**
     * Different customer type.
     */
    const CUSTOMER_CATEGORY = 'Person';

    public function toOptionArray()
    {
        return [
                    [
                        'value' => self::CUSTOMER_CATEGORY,
                        'label' => __('Person')
                    ]
        ];
    }
}
