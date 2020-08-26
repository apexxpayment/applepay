<?php
/**
 * Custom payment method in Magento 2
 * @category    ApplePay
 * @package     Apexx_Applepay
 */
namespace Apexx\Applepay\Model\Adminhtml\Source;

/**
 * Class ThreedPreferences
 * @package Apexx\Applepay\Model\Adminhtml\Source
 */
class ThreedPreferences
{
     public function toOptionArray()
    {
        return [
                ['value' => 'sca', 'label' => __('sca (sca)')],
                ['value' => 'frictionless', 'label' => __('frictionless (frictionless)')],
                ['value' => 'nopref', 'label' => __('nopref (nopref)')],
                ['value' => 'scamandate', 'label' => __('scamandate (scamandate)')],
        ];
    }
}
