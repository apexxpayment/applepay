<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Model\Adminhtml\Source;

/**
 * Class ThreedMode
 * @package Apexx\Applepay\Model\Adminhtml\Source
 */
class ThreedMode
{
    public function toOptionArray()
    {
        return [
                    ['value' => 'sca', 'label' => __('sca (sca)')],
                    ['value' => 'frictionless', 'label' => __('frictionless (frictionless)')],
        ];
    }
}
